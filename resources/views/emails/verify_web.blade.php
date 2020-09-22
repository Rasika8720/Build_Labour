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
								Hi <?php echo $user->getWelcomeName(); ?>,
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
											<p>Click <b><a href="{{ route('verify', "tk={$user->verification_code}") }}">here</a></b> to verify your account</p>
											<p></p>
										</td>
									</tr>
									<tr>
										<td class="content-block">
											<p>If you did not request for confirmation email, no further action is required.</p>
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