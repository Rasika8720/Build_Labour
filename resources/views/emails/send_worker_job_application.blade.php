<!-- Send email to the company email -->
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
								Hi {{ $job->company->name }}
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
											<p>{{ $job->jobApplicantUser->full_name }} applied for a position in <b>{{$job->title}}</b>.</p>
                                            <p>
                                                <a href="{{$job->url}}">
                                                    Click here to view your applicants
                                                </a>
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
