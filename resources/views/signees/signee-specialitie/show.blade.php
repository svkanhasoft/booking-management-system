@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">SigneeSpecialitie {{ $signeespecialitie->id }}</div>
                    <div class="card-body">

                        <a href="{{ url('/signees/signee-specialitie') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/signees/signee-specialitie/' . $signeespecialitie->id . '/edit') }}" title="Edit SigneeSpecialitie"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('signees/signeespecialitie' . '/' . $signeespecialitie->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete SigneeSpecialitie" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $signeespecialitie->id }}</td>
                                    </tr>
                                    <tr><th> User Id </th><td> {{ $signeespecialitie->user_id }} </td></tr><tr><th> Speciality Id </th><td> {{ $signeespecialitie->speciality_id }} </td></tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
