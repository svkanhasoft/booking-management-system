<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head id="Head1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Offer Letter</title>
        <!--<link id="linkStyleSheet" href="http://localhost/laravel_pdf/public/css/offer-letter.css" rel="stylesheet" type="text/css" media="screen" />-->
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

            .break {page-break-after: always}

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
                background-color:#0190CC;
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
                vertical-align:top;
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
            .content-block .heading  {
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
            .content-block  h2  span {
                font-size: 14px;
                font-weight: normal;
            }
            .content-block li {
                margin: 4px;
            }

            .headtable {
                width: 100%;
            }
            .headtable  p   {
                margin-top: 0px;
                margin-right: 0px;
                margin-bottom: 8px;
                margin-left: 0px;
            }
            .headtable .date {
                text-align: right;
                vertical-align: top;
                font-size: 12px;
                font-weight: bold;
            }
            .infotable {
                width: 100%;
                border-top-width: 1px;
                border-right-width: 1px;
                border-left-width: 1px;
                border-top-style: solid;
                border-right-style: solid;
                border-left-style: solid;
                border-top-color: #CCCCCC;
                border-right-color: #CCCCCC;
                border-left-color: #CCCCCC;
            }
            .infotable th {
                text-align: left;
                padding-right: 4px;
                padding-left: 4px;
                color: #0161a3;
                font-size: 11px;
                padding-top: 1px;
                padding-bottom: 1px;
                border-bottom-width: 1px;
                border-bottom-style: solid;
                border-bottom-color: #CCCCCC;
            }


            .infotable td {
                padding-top: 1px;
                padding-right: 4px;
                padding-bottom: 1px;
                padding-left: 4px;
                border-bottom-width: 1px;
                border-bottom-style: solid;
                border-bottom-color: #CCCCCC;

            }
            .subhead{
                padding: 8px;
                font-weight: bold;
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
                /*background: #eee url(../img/bullet2.gif) repeat-x left top;*/
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

            .blue-text{
                color: #0164bb !important;
            }

            .table-block td.alt {
                color: #333;
            }

            div.archive {
                padding: 4px;
                background-color: #e7f8ff;
                border-top-width: 1px;
                border-right-width: 1px;
                border-bottom-width: 1px;
                border-left-width: 1px;
                border-top-style: solid;
                border-right-style: solid;
                border-bottom-style: solid;
                border-left-style: solid;
                border-top-color: #fff;
                border-right-color: #d6f4ff;
                border-bottom-color: #d6f4ff;
                border-left-color: #fff;

            }

            div.archive .archive_t td {
                padding-left: 8px;
                padding-top: 4px;
                padding-bottom: 4px;
                padding-right: 20px;
                font-weight: bold;

            }

            div.archive .archive_t .scroll {
                font-size: 1.3em;
            }


            div.formColumn {
                float: left;
                width: 600px;
                display: inline;
                background-color: #fff;
                padding: 20px;
            }
            div.formColumn .buttonWrap {
                float: right;
                text-align: right;
                padding-top: 0px;
                padding-right: 0px;
                padding-bottom: 0px;
                padding-left: 12px;
                display: inline;

            }
            div.formColumn .discrip {
                font-style: italic;
                /*backgro/und-image: url(../img/icons/exclaim.png);*/
                background-repeat: no-repeat;
                padding-left: 20px;
                background-position: left center;

            }
            div.formColumn fieldset {
                border: 1px solid #0161a3;
                margin-bottom: 20px;
                padding-top: 8px;
                padding-right: 8px;
                padding-bottom: 8px;
                padding-left: 20px;

            }

            div.formColumn legend {
                font-size: 12px;
                padding-right: 10px;
                padding-left: 10px;
                color: #0161a3;
                font-weight: bold;
                text-transform: capitalize;
            }

            .formTable {
                margin-top: 10px;
                margin-right: 0px;
                margin-bottom: 10px;
                margin-left: 0px;
                width: 100%;
            }

            .formTable td {
                vertical-align: top;
                border-bottom-width: 1px;
                border-bottom-style: solid;
                border-bottom-color: #eefaff;
                padding-top: 4px;
                padding-bottom: 4px;
                padding-left: 4px;
                padding-right: 10px;
                width: 50%;
            }


            .formTable .required {
                color: #0161a3;
                padding: 0px;
                margin: 0px;
                font-weight: lighter;
                font-style: italic;
                font-size: 0.9em;
            }

            .formTable td span.info {
                color: #011c2b;
                font-style: italic;
                display: block;
                padding-top: 0px;
                padding-right: 0px;
                padding-bottom: 0px;
                padding-left: 0px;
                font-size: 10px;
                margin: 0px;
                width: 22em;
                float: left;
            }
            .formTable h3 {
                color: #011c2b;
                padding-right: 8px;
                padding-bottom: 0px;
                padding-left: 8px;
                padding-top: 0px;
                line-height: 32px;
                /*background-image: url(../img/bg_header.jpg);*/
                background-repeat: no-repeat;
                background-position: left top;
                font-size: 14px;
            }
            .formTable input:focus, textarea:focus {
                background-color: #EEFAFF;
                border: 1px solid #7f9db9;



            }
            .formTable .inputSelect {
                font-size: 1.2em;
            }
            .formTable .inputField {
                width: 220px;
                float: left;
            }
            .formTable .textField {
                padding: 4px;
                height: 50px;
                width: 218px;
                font-size: 1.1em;
                color: #011c2b;
                font-family: Arial, Helvetica, sans-serif;
            }


            .formTable .inputPostCode {
                width: 80px;
                float: left;
            }
            .formTable  .inputTel {
                width: 136px;
            }
            .formTable .input_cc {
                width: 30px;
            }

            .formTable .inputPword {
                width: 120px;
                float: left;
            }
            .formTable .inputDayMonth {
                width: 20px;
                float: left;
                margin-right: 8px;
            }
            .formTable .inputYear {
                width: 60px;
                float: left;
            }
            .formTable input.button{
                height: 30px;
                /*background-image: url(../img/button_bgr2.jpg);*/
                background-position: left top;
                color: #eefaff;
                border-top-width: 1px;
                border-right-width: 1px;
                border-bottom-width: 1px;
                border-left-width: 1px;
                border-top-style: solid;
                border-right-style: solid;
                border-bottom-style: solid;
                border-left-style: solid;
                border-top-color: #96e8ff;
                border-right-color: #011c2b;
                border-bottom-color: #011c2b;
                border-left-color: #96e8ff;
                margin: 0px;
                padding-top: 0px;
                padding-right: 8px;
                padding-bottom: 0px;
                padding-left: 8px;
            }
            .formTable  input.button:hover{
                /*background-image: url(../img/button_bgr2.jpg);*/
                background-position: bottom;
            }
        </style>
    </head>
    <body style="background-color: White;">
    <header>
        <!-- <img src="http://clientbooking.kanhasoftdev.com/static/media/logo.48531655.svg" width="100%" height="100%"/> -->
        <img src="http://backendbooking.kanhasoftdev.com/uploads/logo.png" width="100%" height="80%"/>
        <h1>{{ $title }}</h1>
    </header>
        <form name="form1" id="form1">
            <div id="offer-letter-1">
                <div id="container">
                <!-- <div style="margin-top: 8rem;">
                    <p style="float: right; font-size : 16px">Date : {{ $date }}</p></h3>
                </div> -->
                    <div class="content-block">
                            <table class="table-block">
                                <tr>
                                    <td class="blue-text">
                                        Name
                                    </td>
                                    <td>
                                        <span id="lblAccName">{{ $data['first_name'] }} {{ $data['last_name'] }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="blue-text">
                                        Email
                                    </td>
                                    <td>
                                        <span id="lblBSB">{{ $data['email'] }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="blue-text">
                                        Contact Number
                                    </td>
                                    <td>
                                        <span id="lblAccNo">{{ $data['contact_number'] }}</span>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td class="blue-text">
                                        Address
                                    </td>
                                    <td>
                                        <span id="lblAccNo">{{ $data['address_line_1'] }} {{ $data['address_line_2'] }}</span>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td class="blue-text">
                                        Rate
                                    </td>
                                    <td>
                                        <span id="lblSwift">{{ $data['rate'] }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="blue-text">
                                        Speciality
                                    </td>
                                    @foreach($data['speciality'] as $key=>$val)
                                    <td>
                                        <span id="lblSwift">{{ $data['speciality'][$key]['speciality_name'] }}</span>
                                    </td>
                                    @endforeach
                                </tr>
                            </table>

                    </div>
                </div>
            </div>
        </form>
        <footer>
        <!-- <img src="footer.png" width="100%" height="100%"/> -->
        <!-- Copyright Pluto @ 2021 -->
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2); height:35px; width:100%; padding-top:1px">
            <h3>© Copyright:
            <a class="text-dark" href="http://clientbooking.kanhasoftdev.com/login">Pluto</a></h3>
        </div>
    </footer>
    </body>
</html>