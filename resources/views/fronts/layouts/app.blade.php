<!DOCTYPE html>
<html dir="ltr" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="author" content="{{ getAppName() }}" />
    <link rel="icon" href="{{ asset(getAppFavicon()) }}" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- META ============== -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- OG -->
    <meta name="robots" content="index, follow">

    <!-- Google Fonts -->
    <link rel="preconnect" href="//fonts.googleapis.com">
    <link rel="preconnect" href="//fonts.gstatic.com" crossorigin>
    <link
        href="//fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />


    <link href="{{ mix('css/front-third-party.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ mix('css/front-pages.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/css/bootstrap-datepicker/bootstrap-datepicker.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/intlTelInput.css') }}">

    <!-- Document Title ===================== -->
    <title>@yield('front-title') | {{ getAppName() }}</title>
    <script src="{{ asset('messages.js') }}"></script>
    <script src="{{ asset('assets/front/vendor/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ mix('js/front-third-party.js') }}"></script>
    <script src="{{ mix('js/front-pages.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <!-- JavaScript Bundle with Popper -->
    <script data-turbo-eval="false">
        let currencyIcon = '{{ getCurrencyIcon() }}'
        let isSetFirstFocus = false
        let csrfToken = "{{ csrf_token() }}"
        let defaultCountryCodeValue = "{{ getSettingValue('default_country_code') }}"
    </script>
    <script src="//js.stripe.com/v3/"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    @routes

    <script data-turbo-eval="false">
        let appointmentStripePaymentUrl = '{{ url('appointment-stripe-charge') }}';
        let stripe = '';
        @if (config('services.stripe.key'))
            stripe = Stripe('{{ config('services.stripe.key') }}');
        @endif
        let manually = "{{ \App\Models\Appointment::MANUALLY }}";
        let paystack = "{{ \App\Models\Appointment::PAYSTACK }}";
        let paypal = "{{ \App\Models\Appointment::PAYPAL }}";
        let stripeMethod = "{{ \App\Models\Appointment::STRIPE }}";
        let razorpayMethod = "{{ \App\Models\Appointment::RAZORPAY }}";
        let authorizeMethod = "{{ \App\Models\Appointment::AUTHORIZE }}";
        let paytmMethod = "{{ \App\Models\Appointment::PAYTM }}";
        let checkLanguageSession = '{{ checkLanguageSession() }}'
            Lang.setLocale(checkLanguageSession);
        let options = {
            'key': "{{ config('payments.razorpay.key') }}",
            'amount': 0, //  100 refers to 1
            'currency': 'INR',
            'name': "{{ getAppName() }}",
            'order_id': '',
            'description': '',
            'image': '{{ asset(getAppLogo()) }}', // logo here
            'callback_url': "{{ route('razorpay.success') }}",
            'prefill': {
                'email': '', // recipient email here
                'name': '', // recipient name here
                'contact': '', // recipient phone here
                'appointmentID': '', // appointmentID here
            },
            'readonly': {
                'name': 'true',
                'email': 'true',
                'contact': 'true',
            },
            'theme': {
                'color': '#4FB281',
            },
            'modal': {
                'ondismiss': function() {
                    $('.book-appointment-message').css('display', 'block');
                    let response =
                        '<div class="gen alert alert-danger">Appointment created successfully and payment not completed.</div>';
                    $('.book-appointment-message').html(response).delay(5000).hide('slow');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                },
            },
        }
    </script>
    <style>
        @if (app()->getLocale() == "ar")
        header, ul {
            direction: rtl;
        }
        
        .justify-content-end {
            justify-content: space-between!important;
        }
        
        .home-page .appointmnet-section .contact-form {
            direction: rtl;
        }
        
        .form-control.form-control-transparent.appointment-calendar, #templateAppointmentDate{
            padding-right:3rem;
        }
        
        #frontAppointmentBook {
            direction: rtl;
        }
        
        .form-check-label {
            margin-right: 2rem;
        }
        
        .iti--allow-dropdown .iti__flag-container, .iti--separate-dial-code .iti__flag-container {
            right: auto;
            left: auto;
        }
        
        .form-control#phoneNumber {
            padding-right: 6.5rem;
            padding-left: 0;
            text-align: right;
        }
        
        .book-appointment-page .book-appointment-form .slots-box .slots-item {
            direction: ltr;
        }
        
        .add-menu-left ul{
            padding-inline-start: 0!important;
        }
    
        .form-select {
            background-position: left .75rem center;;
        }
        
        .top-heading {
            font-weight: 700;
            letter-spacing: 0;
        }
        
        h5, h2, p {
            direction: rtl;
            text-align: right;
        }
        
        .text-center {
            direction: rtl;
            text-align-last: center;
        }
        
        .input-group:not(.has-validation)>.dropdown-toggle:nth-last-child(n+3), .input-group:not(.has-validation)>:not(:last-child):not(.dropdown-toggle):not(.dropdown-menu) {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }
        
        .input-group>:not(:first-child):not(.dropdown-menu):not(.valid-tooltip):not(.valid-feedback):not(.invalid-tooltip):not(.invalid-feedback) {
            margin-right: -1px;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }
        
        .input-group {
            direction: rtl;
        }
        
        .footer-info__block {
            direction: rtl;
        }
        
        .footer-info__footer-icon {
            margin-left: 1rem;
        }
        
        .footer-info__block a {
            direction: ltr;
        }
        
        .testimonial-section .align-items-center {
            direction: rtl;
        }
        @endif
    </style>
</head>

<body>
    @include('fronts.layouts.header')
    @yield('front-content')
    @include('fronts.layouts.footer')
</body>

</html>
