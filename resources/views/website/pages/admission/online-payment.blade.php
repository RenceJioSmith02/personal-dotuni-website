@extends('layouts.website')

@section('title', 'Online Payment of School Fees  | CLSU DOT-Uni')

@push('css')
    <style>
        .online-payment-section {
            background: #f2f2f2;
            padding: 60px 40px;
        }

        .online-payment-section .content {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .online-payment-section .content-title {
            font-size: 18px;
            text-align: left;
            padding: 0px 10px;
            border-left: #E8D203 3px solid;
        }

        .payment-partners {
            margin-top: 30px;
        }

        .partners-title {
            text-align: center;
            color: #1c7c34;
            margin-bottom: 30px;
            letter-spacing: 1px;
        }

        .partners-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 60px;
        }

        .partner-box {
            padding: 25px;
            border-radius: 6px;
        }

        .instruction {
            border-left: 4px solid #1c7c34;
            padding-left: 12px;
            margin: 15px 0 25px;
        }

        .account-details {
            text-align: center;
            max-width: 400px;
            margin: 0 auto;
        }


        .account-details p {
            margin-bottom: 2px;
        }

        .account-details strong {
            display: block;
            margin-bottom: 12px;
            text-decoration: underline;
        }

        .payment-note {
            text-align: center;
            margin-top: 40px;
            font-size: 14px;
        }



        /* Mobile adjustments */
        @media (max-width: 768px) {
            .online-payment-section {
                padding: 40px 20px;
            }

            .online-payment-section .content-title {
                font-size: 16px;
            }

            .online-payment-section .content {
                max-width: 100%;
            }

            .partners-grid {
                grid-template-columns: 1fr;
                gap: 25px;
            }
        }
    </style>
@endpush

@section('content')

    <section class="online-payment-section">
        <div class="container">
            <div class="divider"></div>
            <h2 class="section-title">
                ONLINE PAYMENT OF SCHOOL FEES
            </h2>

            <div class="content">
                <div class="payment-partners">

                    <div class="partners-grid">

                        <!-- PHILIPPINES -->
                        <div class="partner-box">

                            <p class="content-title">If you are in the Philippines:</p>

                            <div class="instruction">
                                <p style="margin: 0">Go to the nearest Land Bank of the Philippines Branch</p>
                                <p style="margin: 0">Get a deposit slip and fill it up:</p>
                            </div>

                            <div class="account-details">
                                <p>Account Name:</p>
                                <strong>CLSU Open University</strong>

                                <p>Account Number:</p>
                                <strong>2962-1006-89</strong>

                                <p>LandBank Branch:</p>
                                <strong>Science City of Muñoz, Nueva Ecija, Philippines</strong>
                            </div>

                        </div>

                        <!-- ABROAD -->
                        <div class="partner-box">

                            <p class="content-title">If you are abroad or outside the Philippines:</p>

                            <div class="instruction">
                                <p style="margin: 0">Go to the nearest Bank/Remittance Center for money transfer.</p>
                                <p style="margin: 0">Get a deposit slip and fill it up:</p>
                            </div>

                            <div class="account-details">
                                <p>Account Name:</p>
                                <strong>CLSU Open University</strong>

                                <p>Dollar Account Number:</p>
                                <strong>2964-0028-62</strong>

                                <p>Peso Account Number:</p>
                                <strong>2962-1006-8</strong>

                                <p>Swift code:</p>
                                <strong>TLB-PH-MM</strong>

                                <p>LandBank Branch:</p>
                                <strong>Science City of Muñoz, Nueva Ecija, Philippines</strong>
                            </div>

                        </div>

                    </div>

                    <div class="payment-note">
                        <p style="text-align: left;
                                    padding: 0px 10px;
                                    border-left: #E8D203 3px solid;">
                            Pay the assessed school fees due as stated in Statement of Account, which the DOTUni will e-mail
                            to you.
                        </p>

                        <p>
                            After paying the assessed fees, email the receipt to
                            <u>dotregistrar@clsu.edu.ph</u> or
                            <u>admission@dotclsu.edu.ph</u>
                        </p>

                        <p>
                            Wait for your confirmation of receipt of payment through e-mail.
                        </p>
                    </div>

                </div>

            </div>

        </div>
    </section>

@endsection