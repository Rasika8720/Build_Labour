<?php


namespace App\Http\Controllers\Api\V1;

use App\Models\Photos\Photos;
use App\Models\Reports\Reports;
use App\Models\Users\Users;
use Illuminate\Http\Request;

class ApiPhotosController extends ApiBaseController{

    public function upload( Request $r )
    {
        if( ! $user = $this->authenticate( $r )){
            return response()->json( $this->response , $this->response['status'] );
        }

        $user = ( new Users )->f( $user->id );
        $photo_type = $r->photo_type ? $r->photo_type : 'profile';
        $is_primary = 0;

        if( $r->photo_type == 'profile' ){
            $is_primary = '1';
        }

        // check if there is a primary photo
        // set as primary if no primary photo were found
        $has_primary = Photos::where( 'is_primary', 1 )
            ->where( 'user_id' , $user->id  )->count( );

        if( ! $has_primary ){
            if( in_array( $photo_type , ['gallery' , 'profile' ])){
                $is_primary = 1;
            }
        }

        $r->merge( [ 'photo_type' => $photo_type, 'is_primary' => $is_primary ] );

        if( ! $photo = $user->upload( $r ) ){
            return response()->json(
            [
                'status' => 400,
                'success' => false,
                'error_code' => 'uploadError',
                'message'=> $user->getErrors( true )
            ],
                400
            );
        }

        return response()->json(
            [
                'status' => 200,
                'success' => true,
                'message'=> 'success',
                'data' => [
                    'photo' => $photo,
                    'user' => $user,
                    'is_primary' => $r->is_primary
                ]
            ],
            200
        );

    }


    public function getGalleryPhotos( Request $r )
    {
        // required token to get user gallery photos
        if( ! $user = $this->authenticate( $r )){
            return response()->json( $this->response , $this->response['status'] );
        }

        if( ! $r->user_id ){
            return response()->json(
                [
                    'status' => 400,
                    'success' => false,
                    'error_code' => 'invalidUserId',
                    'message'=> 'Invalid User Id'
                ],
                400
            );
        }

        $gallery_owner = Users::find( $r->user_id );

        $photos_obj = new Photos();
        $r->merge( [ 'photo_type' => [ 'gallery' ] , 'user_id' => $r->user_id ] );
        $photos = $photos_obj->getCollection( $r );

        return response()->json(
            [
                'status' => 200,
                'success' => true,
                'message'=> 'success',
                'data' => [
                    'photos' => $photos,
                    'gallery_owner' => $gallery_owner
                ]
            ],
            200
        );

    }


    public function delete( Request $r )
    {
        if( ! $user = $this->authenticate( $r )){
            return response()->json( $this->response , $this->response['status'] );
        }
        // want to use User Model instead of the JWT user model
        $user = ( new Users )->f( $user->id );

        if( ! $r->photo_id ){
            return response()->json(
                [
                    'status' => 400,
                    'success' => false,
                    'error_code' => 'invalidPhotoId',
                    'message'=> 'Invalid Photo ID'
                ],
                400
            );
        }

        $photo = ( new Photos )->f( $r->photo_id );
        if( ! $photo ){
            return response()->json(
                [
                    'status' => 400,
                    'success' => false,
                    'error_code' => 'invalidPhotoId',
                    'message'=> 'Invalid Photo ID'
                ],
                400
            );
        }

        if( $photo->user_id != $user->id ){
            return response()->json(
                [
                    'status'     => 400,
                    'success'    => false,
                    'error_code' => 'invalidPhotoOwner',
                    'message'    => 'You do not have permission to delete photo'
                ],
                400
            );
        }

        $file_path      = $photo->file_path;
        $thumb_path     = $photo->getThumbNailPath();
        $xs_path = $photo->getThumbNailXsPath();

        $photo_url = $photo->url;

        $photo->delete();
        // unlink the photos
        if( is_file( $file_path ) ){
            unlink( $file_path );
        }

        if( is_file( $thumb_path ) ){
            unlink( $thumb_path );
        }

        if( is_file( $xs_path ) ){
            unlink( $xs_path );
        }

        $profile_photo_url = null;

        // check if photo is the profile photo of the user
        if( $photo_url == $user->profile_photo_url ){

            // check if there are other photos in the gallery
            // use the latest
            if( $next_photo = $user->nextProfilePhoto() ){
                $profile_photo_url = $next_photo->url;
                $next_photo->is_primary = '1';
                $next_photo->save();
            }

            $user->profile_photo_url = $profile_photo_url;
            $user->save();
        }

        return response()->json(
            [
                'status' => 200,
                'success' => true,
                'message'=> 'success',
                'data' => [
                    'photo_id' => $r->photo_id,
                    'file_path'  => $file_path,
                    'thumb_path' => $thumb_path,
                    'xs_path'    => $xs_path,
                    'new_profile_photo' => $profile_photo_url,
                    'user' => $user
                ]
            ],
            200
        );
    }

    public function setPrimary( Request $r )
    {
        if( ! $user = $this->authenticate( $r )){
            return response()->json( $this->response , $this->response['status'] );
        }
        // want to use User Model instead of the JWT user model
        $user = ( new Users )->f( $user->id );

        if( ! $r->photo_id ){
            return response()->json(
                [
                    'status' => 400,
                    'success' => false,
                    'error_code' => 'emptyPhotoId',
                    'message'=> 'Photo ID must not be empty',
                    'user_id' => $user->id,
                    'photo_id' => $r->photo_id
                ],
                400
            );
        }

        $photo = ( new Photos )->f( $r->photo_id );
        if( ! $photo ){
            return response()->json(
                [
                    'status' => 400,
                    'success' => false,
                    'error_code' => 'invalidPhotoId',
                    'message'=> 'Invalid Photo ID',
                    'user_id' => $user->id,
                    'photo_id' => $r->photo_id
                ],
                400
            );
        }

        if( $photo->user_id != $user->id ){
            return response()->json(
                [
                    'status'     => 400,
                    'success'    => false,
                    'error_code' => 'invalidPhotoOwner',
                    'message'    => 'You do not have permission to update photo'
                ],
                400
            );
        }

        // set all user photos primary photos of the user to 0
        Photos::where( 'user_id' , $user->id  )
            ->where( 'is_primary' , 1 )
            ->update( [ 'is_primary' => 0 ] );

        $photo->is_primary = '1';
        $photo->save();

        // update the user photo_url
        $user->profile_photo_url =  $photo->url;
        $user->save();

        return response()->json(
            [
                'status' => 200,
                'success' => true,
                'message'=> 'success',
                'data' => [
                    'photo_id' => $r->photo_id,
                    'photo' => $photo,
                    'user' => $user
                ]
            ],
            200
        );

    }
}