<?php

namespace App\Validators;

class RegisterUserValidator
{
	public static function rules()
	{
		return [
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|string|min:8|max:50|confirmed',
            'first_name'    => 'nullable|string',
            'last_name'     => 'nullable|string',
            'date_of_birth'           => 'nullable|date|date_format:Y-m-d',
            'country'       => 'nullable|string',
            'mobile_number' => 'nullable|string',
            'address'       => 'nullable|string',
        ];
	}

	public static function messages()
	{
		return [
            'email.required'     => 'Please fill in your e-mail address.',
            'email.max'          => 'E-mail address may not be greater than 255 characters.',
            'email.email'        => 'This e-mail address format is invalid.',
            'email.unique'       => 'This e-mail has been used already for registration.',

            'password.required'  => 'Please fill in your password.',
            'password.max'       => 'Password may not be greater than 50 characters',
            'password.min'       => 'Password must be at least 8 characters',
            'password.confirmed' => 'Passwords do not match',
        ];
	}

}