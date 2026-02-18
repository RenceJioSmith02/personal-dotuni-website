@extends('layouts.website')

@section('title', 'Admission Requirements | CLSU DOT-Uni')

@push('css')
<style>
    .document-section {
        background: #f2f2f2;
        padding: 60px 40px;
    }

    .document-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px;
        border-radius: 6px;
    }

    .document-header {
        margin-bottom: 25px;
    }

    .document-article {
        border-left: 3px solid #E8D203; 
        padding-left: 10px;
        margin-bottom: 10px;
    }

    .document-title {
        text-align: center;
    }

    .document-body {
        border-left: 3px solid #1c7c34;
        padding-left: 20px;
        text-align: justify;
        line-height: 1.7;
    }

    .document-body p {
        margin-bottom: 18px;
    }

    .document-list {
        padding-left: 20px;
    }

    .document-list ul {
        list-style-type: disc;
        padding-left: 20px;
        margin-top: 10px;
    }

    .document-list li {
        margin-bottom: 10px;
    }
</style>
@endpush

@section('content')


<section class="document-section">

    <div class="divider"></div>
    <h2 class="section-title">
        INFORMATION for PROSPECTIVE STUDENTS
    </h2>

    <div class="content">
        <div class="document-container">

            <div class="document-header">
                <h4 class="document-article">ARTICLE II</h4>
                <h5 class="document-title">Declaration of Policy</h5>
            </div>

            <div class="document-body">

                <p>
                    <strong>Section 1 :</strong>
                    It is hereby declared the policy of the Distance, Open,
                    and Transnational University to promote and protect the
                    right of Filipino citizens to quality education...
                </p>

                <p>
                    <strong>Section 2 :</strong>
                    It is likewise the policy of the Distance, Open,
                    and Transnational University to uphold CLSU’s
                    institutional vision...
                </p>

                <p>
                    <strong>Section 3 :</strong>
                    It is also the policy of the Distance, Open,
                    and Transnational University...
                </p>

                <ol class="document-list">
                    <li>
                        Presented and Approved by the Council of Deans...
                        <ul>
                            <li>A school year at DOT-Uni starts...</li>
                            <li>The First Term starts on August...</li>
                            <li>The start of the programs...</li>
                        </ul>
                    </li>

                    <li>
                        Presented and Approved by the Academic Council...
                    </li>

                    <li>
                        Approved by the Board of Regents...
                    </li>
                </ol>

            </div>

        </div>
    </div>

</section>


@endsection
