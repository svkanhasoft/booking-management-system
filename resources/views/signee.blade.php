<!DOCTYPE html>
<html>
<head>
    <title>Hi</title>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>{{ $date }}</p><br><hr>
    <p><b>ID</br> : {{ $data['id'] }}</p>
    <p><b>First Name</b> : {{ $data['first_name'] }}</p>
    <p><b>Last Name</b> : {{ $data['last_name'] }}</p>
    <p><b>Email</b> : {{ $data['email'] }}</p>
    <p><b>Contact Number</b> : {{ $data['contact_number'] }}</p>
    <p><b>Organisation Person First Name</b> : {{ $data['org_first_name'] }}</p>
    <p><b>Organisation Person Last Name</b> : {{ $data['org_last_name'] }}</p>
    <p><b>Organisation Email</b> : {{ $data['org_email'] }}</p>
    <p><b>Organisation Name</b> : {{ $data['organization_name'] }}</p>
    <p><b>Address 1</b> : {{ $data['address_line_1'] }}</p>
    <p><b>Address 2</b> : {{ $data['address_line_2'] }}</p>
    <p><b>Candidate Id</b> : {{ $data['candidate_id'] }}</p>
    <p><b>City</b> : {{ $data['city'] }}</p>
    <p><b>Postcode</b> : {{ $data['postcode'] }}</p>
    <p><b>Nationality</b> : {{ $data['nationality'] }}</p>
    <p><b>Date Of Birth</b> : {{ $data['date_of_birth'] }}</p>
    <p><b>Refered From</b> : {{ $data['candidate_referred_name'] }}</p>
    <p><b>NMC-DMC PIN</b> : {{ $data['nmc_dmc_pin'] }}</p>
    <p><b>Date Registered</b> : {{ $data['date_registered'] }}</p>
    @foreach($data['speciality'] as $key=>$val)
        <p><b>Speciality</b> : {{ $data['speciality'][$key]['speciality_name'] }}</p>
    @endforeach
    
</body>
</html>