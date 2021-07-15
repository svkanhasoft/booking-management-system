@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">OrganizationShift {{ $organizationshift->id }}</div>
                    <div class="card-body">

                        <a href="{{ url('/organization/organization-shift') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/organization/organization-shift/' . $organizationshift->id . '/edit') }}" title="Edit OrganizationShift"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('organization/organizationshift' . '/' . $organizationshift->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete OrganizationShift" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $organizationshift->id }}</td>
                                    </tr>
                                    <tr><th> User Id </th><td> {{ $organizationshift->user_id }} </td></tr><tr><th> Start Time </th><td> {{ $organizationshift->start_time }} </td></tr><tr><th> End Time </th><td> {{ $organizationshift->end_time }} </td></tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
