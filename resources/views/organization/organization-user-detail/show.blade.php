@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">OrganizationUserDetail {{ $organizationuserdetail->id }}</div>
                    <div class="card-body">

                        <a href="{{ url('/organization/organization-user-detail') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/organization/organization-user-detail/' . $organizationuserdetail->id . '/edit') }}" title="Edit OrganizationUserDetail"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('organization/organizationuserdetail' . '/' . $organizationuserdetail->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete OrganizationUserDetail" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $organizationuserdetail->id }}</td>
                                    </tr>
                                    <tr><th> User Id </th><td> {{ $organizationuserdetail->user_id }} </td></tr><tr><th> Contact Number </th><td> {{ $organizationuserdetail->contact_number }} </td></tr><tr><th> Role Id </th><td> {{ $organizationuserdetail->role_id }} </td></tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
