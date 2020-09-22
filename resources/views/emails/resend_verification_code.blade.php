<!-- Verification Email -->
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
								Hi <?php echo $user->first_name ?>,
							</td>
						</tr>
						<tr>
							<td class="content-wrap">
								<table width="100%" cellpadding="0" cellspacing="0">
									<tr>
										<td class="content-block">

										</td>
									</tr>
									<tr>
										<td class="content-block">
											<p>Just copy and paste the following code in app's confirm email form to verify your account:</p>
											<p><b>{{ $user->verification_code }}</b></p>
										</td>
									</tr>
									<tr>
										<td class="content-block">
											<p>If you did not request a confirmation token, no further action is required.</p>
										</td>
									</tr>
									<tr>
										<td class="content-block">
											<p>
												<b>{{ config('app.name', 'Appetiser') }}</b>
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