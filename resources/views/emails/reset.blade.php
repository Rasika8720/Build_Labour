<!-- Reset Password Email -->
@extends('layouts.emails')

@section('content')
	<table class="body-wrap">
		<tr>
			<td></td>
			<td class="container" width="700">
				<div class="content">
					<table class="main" width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td class="alert alert-good">
							Reset Password
						</td>
					</tr>
					<tr>
						<td class="content-wrap">
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td class="content-block">
									<p> <b>Hi <?php echo $user->first_name ?></b> </p>
									<p>You are receiving this email because we received a password reset request for your account.</p>
								</td>
							</tr>
							<tr>
								<td class="content-block">
									<p>Just click the following link to reset your password:</p>
                                    <p><b><a href="{{ route('password_reset', "token={$token}&email={$user->email}") }}">Reset Password</a></b></p>
								</td>
							</tr>
							<tr>
								<td class="content-block">
									<p>If you didn't make this request, there may be an attempted security breach to your account. Please check your details or contact us at <a href="mailto:info@buildlabour.com.au" target="_top">info@buildlabour.com.au</a></p>
								</td>
							</tr>
							<tr>
								<td class="content-block">
									<p>
										<b><?php echo env( 'APP_NAME' ) ?></b>
									</p>
								</td>
							</tr>
						</table>
						</td>
					</tr>
					</table>
				</div>
			</td>
			<td></td>
		</tr>
	</table>
@endsection