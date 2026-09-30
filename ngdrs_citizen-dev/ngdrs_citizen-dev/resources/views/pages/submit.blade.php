<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>MATi eSign</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <style>

        * {
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:
            "Segoe UI",
            Arial,
            sans-serif;
        }


        body {

            min-height:100vh;

            background:
            linear-gradient(
                135deg,
                #f4f7fb,
                #e8eef8
            );

            display:flex;
            justify-content:center;
            align-items:center;

        }


        .esign-container {

            width:420px;

            background:#ffffff;

            border-radius:16px;

            box-shadow:
            0 10px 30px
            rgba(0,0,0,0.12);

            overflow:hidden;

        }



        .header {

            background:
            linear-gradient(
                135deg,
                #1e3c72,
                #2a5298
            );

            padding:25px;

            color:white;

            text-align:center;

        }



        .header .logo {

            width:65px;
            height:65px;

            background:white;

            border-radius:50%;

            display:flex;

            justify-content:center;
            align-items:center;

            margin:auto;

            color:#1e3c72;

            font-size:28px;

            font-weight:bold;

            margin-bottom:15px;

        }



        .header h2 {

            font-size:22px;

            font-weight:600;

        }



        .header p {

            margin-top:8px;

            font-size:14px;

            opacity:.9;

        }



        .body {

            padding:35px;

            text-align:center;

        }



        .loader {

            width:55px;

            height:55px;

            border-radius:50%;

            border:

            5px solid #e0e6ef;

            border-top-color:#1e3c72;

            animation:
            spin 1s linear infinite;

            margin:
            0 auto 25px;

        }



        @keyframes spin {

            from {
                transform:rotate(0deg);
            }

            to {
                transform:rotate(360deg);
            }

        }



        .body h3 {

            color:#333;

            font-size:18px;

            margin-bottom:10px;

        }



        .body p {

            color:#666;

            font-size:14px;

            line-height:1.6;

        }



        .footer {

            background:#f8fafc;

            padding:15px;

            text-align:center;

            font-size:12px;

            color:#777;

        }



        .secure {

            color:#198754;

            font-weight:600;

        }


    </style>


</head>


<body>



<div class="esign-container">


    <div class="header">

        <div class="logo">
            N
        </div>


        <h2>
            MATi eSign
        </h2>


        <p>
            Digital Signature Authentication
        </p>

    </div>



    <div class="body">


        <div class="loader"></div>


        <h3>
            Processing eSign Request
        </h3>


        <p>
            Please wait while we securely redirect you
            to the eSign authentication service.
        </p>


        <br>


        <p class="secure">
            🔒 Secure Digital Signing Enabled
        </p>


    </div>



    <div class="footer">

        MATi Document Registration System
        <br>

        Powered by NIC

    </div>


</div>




<form 
    action="{{ route('esign.index_new') }}"
    method="POST"
    id="esignForm"
>


    @csrf


    <input 
        type="hidden"
        name="eSignRequest"
        value="{!! htmlentities($signXML) !!}"
    >



    <input 
        type="hidden"
        name="aspTxnID"
        value="{{ $txn }}"
    >



    <input 
        type="hidden"
        name="responseUrl"
        value="{{ route('esign.response') }}"
    >



    <input 
        type="hidden"
        name="Content-Type"
        value="application/xml"
    >


</form>



<script>

setTimeout(function(){

    document
    .getElementById('esignForm')
    .submit();


},1500);


</script>



</body>

</html>