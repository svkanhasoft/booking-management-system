<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head id="Head1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Booking Details</title>
    <style>
        body {
            background: #666666;
            margin: 0;
            padding: 0;
            text-align: center;
            color: #333;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 65%;
        }

        .page-break {
            page-break-after: always;
        }

        #container {
            /*width: 800px;*/
            background: #FFFFFF;
            border: 0px solid #000000;
            text-align: left;
            margin-top: 0;
            /*margin-right: auto;*/
            margin-bottom: 0;
            /*margin-left: auto;*/
        }

        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 3cm;
        }

        footer {
            position: fixed;
            bottom: 2px;
            left: 12px;
            right: 0px;
            height: 2px;
        }

        .header {
            height: 125px;
            padding-top: 0;
            padding-right: 0px;
            padding-bottom: 8px;
            padding-left: 0px;
            background-repeat: no-repeat;
            background-position: right top;
            margin-bottom: 15px;
        }

        .header .redbar {
            background-color: #BF0B0B;
            height: 15px;
        }

        .header .bluebar {
            background-color: #0190CC;
        }

        .header .addinfo {
            color: #666666;
            text-align: left;
            vertical-align: top;
            padding-top: 8px;
            padding-right: 4px;
            padding-bottom: 8px;
            padding-left: 8px;
            font-size: 10px;
            width: 350px;
        }

        .header .addinfo h4 {
            margin: 0px;
            padding: 0px;
            font-size: 11px;
            font-weight: bold;
        }

        .header .borderleft {
            border-left-width: 1px;
            border-left-style: solid;
            border-left-color: #CCCCCC;
            padding-left: 5px;
            margin-top: 3px;
            margin-bottom: 3px;
            width: 150px;
        }

        .header .addinfo td {
            padding-right: 5px;
            vertical-align: top;
        }

        .header .address {
            float: right;
            color: #fff;
            text-align: right;
            font-size: 70%;
            padding-right: 10px;
            margin-top: 20px;
        }

        .header h1 {
            color: #fff;
            float: left;
        }

        .content-block .heading {
            text-align: center;
            line-height: 25px;
            font-size: 119%;
            font-weight: bolder;
            margin-top: 0px;
            margin-right: auto;
            margin-bottom: 0px;
            margin-left: auto;
            padding-bottom: 0px;
        }

        .content-block {
            background: #FFFFFF;
            padding-top: 0;
            padding-right: 20px;
            padding-bottom: 0;
            padding-left: 20px;
            margin-top: 110px;
            margin-right: 15px;
            margin-bottom: 5px;
            margin-left: 15px;
            /*height:100% auto;*/
        }

        .content-block h1 {
            border-bottom-width: 1px;
            border-bottom-style: solid;
            border-bottom-color: #333333;
            font-size: 119%;
        }

        .content-block h2 {
            font-size: 95%;
        }

        .content-block h3 {
            font-size: 90%;
        }

        .content-block h4 {
            font-size: 85%;
        }

        .content-block h2 span {
            font-size: 14px;
            font-weight: normal;
        }

        .content-block li {
            margin: 4px;
        }

        .table-block {
            border-width: 0px;
            width: 100%;
            border-style: solid;
            border-color: #fff;
            margin-top: 2rem;
            border-collapse: collapse;
            font-size: 16px;
        }

        .table-block caption {
            text-align: right;
            padding-top: 20px;
            padding-right: 0;
            padding-bottom: 0px;
            padding-left: 0;
            font-family: "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
            font-style: italic;
            color: #333;
        }

        .table-block th {
            color: #fcf4ff;
            border-width: 1px;
            border-style: solid;
            border-color: #ccc;
            background-color: #333;
            background-repeat: repeat-x;
            background-position: left top;
            font-family: "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
            font-size: 11px;
            padding-top: 1px;
            padding-right: 4px;
            padding-bottom: 1px;
            padding-left: 4px;
            vertical-align: top;
        }

        .table-block th.nobg {
            border-width: 1px;
            border-style: solid;
            border-color: #ccc;
            color: #333;
            background: #fff url(none);
        }

        .table-block th.spec {
            border-width: 1px;
            border-style: solid;
            border-color: #ccc;
            font-family: "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
            color: #333;
            background-color: #fff;
            /*//background-image: url(../img/bullet1.gif);*/
            background-repeat: no-repeat;
            background-position: left top;
        }

        .table-block th.specalt {
            color: #333;
            border-width: 1px;
            border-style: solid;
            border-color: #ccc;
            font-family: "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
        }

        .table-block tr.alt {
            background: #eee;
        }

        .table-block tr.alt:hover {
            background: #ddd;
        }

        .table-block tr {
            background-color: #fff;
            border-width: 1px;
            border-style: solid;
            border-color: #ccc;
        }

        .table-block tr:hover {
            background: #eee;
        }

        .table-block td {
            /*padding: 6px 6px 6px 12px;*/
            padding: 7px 20px;
            color: #333;
            border-width: 1px;
            border-style: solid;
            border-color: #e7e7e7;
        }

        .blue-text {
            color: #0164bb !important;
        }

        .table-block td.alt {
            color: #333;
        }

        div.formColumn {
            float: left;
            width: 600px;
            display: inline;
            background-color: #fff;
            padding: 20px;
        }
    </style>
</head>


<body style="background-color: White;">
    @php
    $pageCount= 0 ;
    @endphp


    <header  style="background-color: #2B68A4;">
        <img src="http://clientbooking.kanhasoftdev.com/static/media/logo.48531655.svg" width="230px" height="94px" />
    </header>
    <div class="content-block ">
        <h2 style="padding-top: 10px; font-size: 15px;">Booking Details</h2>
        <table class="table-block">
            <tr>
                <td class="blue-text">
                    Booking Name
                </td>
                <td>
                    <span id="lblAccName">{{ $data['booking']['reference_id'] }}</span>
                </td>
            </tr>
            <tr>
                <td class="blue-text">
                    Trust
                </td>
                <td>
                    <span id="lblAccName">{{ $data['booking']['name'] }}</span>
                </td>
            </tr>
            <tr>
                <td class="blue-text">
                    Hospital
                </td>
                <td>
                    <span id="lblAccName">{{ $data['booking']['hospital_name'] }}</span>
                </td>
            </tr>
            <tr>
                <td class="blue-text">
                    Ward
                </td>
                <td>
                    <span id="lblAccName">{{ $data['booking']['ward_name'] }}</span>
                </td>
            </tr>
            <tr>
                <td class="blue-text">
                    Grade
                </td>
                <td>
                    <span id="lblAccName">{{ $data['booking']['grade_name'] }}</span>
                </td>
            </tr>
            <tr>
                <td class="blue-text">
                    Shift Type
                </td>
                <td>
                    <span id="lblAccName">{{ $data['booking']['shift_type'] }}</span>
                </td>
            </tr>
            <tr>
                <td class="blue-text">
                    Payable
                </td>
                <td>
                    <span id="lblAccName">{{ $data['booking']['payable'] }}</span>
                </td>
            </tr>
            <tr>
                <td class="blue-text">
                    Shift Time
                </td>
                <td>
                    <span id="lblAccName">{{ date('H:i', strtotime($data['booking']['start_time'])). '-' .date('H:i', strtotime($data['booking']['end_time'])) }}</span>
                </td>
            </tr>
            {{-- <tr>
                <td class="blue-text">
                    Shift Time
                </td>
                <td>
                    <span id="lblAccName">{{ date('H:i', strtotime($data['booking']['start_time'])) - date('H:i', strtotime($data['booking']['end_time'])) }}</span>
                </td>
            </tr> --}}
        </table>
    </div>
    <footer>
        <div class="text-center p-3" style="background-color: #2B68A4; height:35px; width:100%; padding-top:1px">
            <h3>© Copyright:
                <a href="http://clientbooking.kanhasoftdev.com/login">Pluto</a>
            </h3>
        </div>
    </footer>
    <div class="page-break">
    </div>

    @foreach($data['signee'] as $key=>$val)
    @php
    $pageCount= $pageCount+1;
    @endphp
    @if(!empty($val))
    <header  style="background-color: #2B68A4;">
        <img src="http://clientbooking.kanhasoftdev.com/static/media/logo.48531655.svg" width="230px" height="94px" />
    </header>
    <form name="form1" id="form1">
        <div id="offer-letter-1">
            <div id="container">
                <!-- <div style="margin-top: 8rem;">
                    <p style="float: right; font-size : 16px">Date : {{ $date }}</p></h3>
                </div> -->
                <div class="content-block ">
                    <h2 style="padding-top: 10px; padding-left: 40%; font-size: 15px;">Candidate Details</h2>
                    <table class="table-block">
                        <tr>
                            <td class="blue-text">
                                Name
                            </td>
                            <td>
                                <span id="lblAccName">{{ $val['user_name'] }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="blue-text">
                                Email
                            </td>
                            <td>
                                <span id="lblBSB">{{ $val['email'] }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="blue-text">
                                Contact Number
                            </td>
                            <td>
                                <span id="lblAccNo">{{ $val['contact_number'] }}</span>
                            </td>
                        </tr>

                        <tr>
                            <td class="blue-text">
                                Address
                            </td>
                            <td>
                                <span id="lblAccNo">{{ $val['city'] }} {{ $val['postcode'] }} {{ $val['address_line_1'] }} {{ $val['address_line_2'] }}</span>
                            </td>
                        </tr>
                        {{-- <tr>
                            <td class="blue-text">
                            payable
                            </td>
                            <td>
                                <span id="lblSwift">{{ $val['payable'] }}</span>
                            </td>
                        </tr> --}}
                        <tr>
                            <td class="blue-text">
                                Speciality
                            </td>
                            <td>
                                <span id="lblSwift">{{ $val['speciality_name'] }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
                @if($pageCount < count($data)) <div class="page-break">
            </div>
            @endif
        </div>
        </div>
    </form>
    <footer>
        <div class="text-center p-3" style="background-color: #2B68A4; height:35px; width:100%; padding-top:1px">
            <h3>© Copyright:
                <a href="http://clientbooking.kanhasoftdev.com/login">Pluto</a>
            </h3>
        </div>
    </footer>
    @endif
    @endforeach


</body>


</html>
