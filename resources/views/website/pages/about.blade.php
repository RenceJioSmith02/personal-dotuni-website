@extends('layouts.website')

@section('title', 'About Us | CLSU DOT-Uni')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/website/about.css') }}" />
@endpush

@section('content')

<section class="about-section">
  <div class="about-container">

    <!-- LEFT STICKY SIDE -->
    <div class="about-left">
      <div class="sticky-wrapper">
        <img src="{{ asset('assets/system_images/dotuni.png') }}" alt="DOT UNI">
      </div>
    </div>

    <!-- RIGHT SCROLLABLE SIDE -->
    <div class="about-right">
      <div class="about-content">

        <div class="divider"></div>
        <h2 class="section-title">The CLSU Open University in a Capsule</h2>

        <h4>Creation and Mandate</h4>
        <p>
            The Central Luzon State University Open University (CLSU-OU) was formally created on August 29, 1997 through CLSU Board of Regents Resolution No. 50-97 aligned to the country’s goal of developing quality human resources by democratization of access to quality education. Its mandate is to provide open and distance education opportunities via degree and non-degree programs to individuals from all walks of life aspiring for higher education or for improved qualifications or credentials.
            In 1998, the CLSU-OU initially offered two programs, Master of Science in Education and Master of Science in Rural Development. Early students were teachers from the various municipalities of Nueva Ecija and extension personnel of the Department of Agriculture. The decision to offer more degree and non-degree programs led to an increase in enrolments. This enhanced access to otherwise traditional residential curricular programs led to greater visibility of CLSU-OU.
            After two decades, the institution underwent a major transformation. With the vertical articulation of graduate courses, and the emerging need to reorient towards transnational education, CLSU-OU was renamed into CLSU Distance, Open and Transnational University (CLSU DOT-Uni) by virtue of Board Resolution 16-2018 on February 15, 2018.  Concomitant restructuring and reorganization commenced with a renewed purpose and more aggressive institutional strengthening.
        </p>


        <h4>Mission</h4>
        <p>
            The mission of the CLSU Open University is to provide its students with opportunities to earn formal qualification as well as to develop in them the readiness for lifelong learning in today's knowledge society.
        </p>

        <h4>Objectives</h4>
        <p>With inclusivity at the very core of its mandate, the DOT-Uni shall:</p>
        <ul>
          <li>Provide equitable access to higher education to different sections of society through alternative, non-residential and ICT-mediated teaching-learning delivery system;</li>
          <li>Prioritize public engagement and knowledge transfer, and play a key role in advancing alternative education delivery systems through Distance Education, Open Learning Systems, and Transnational Education by offering key degree and non-degree programs based on the University’s expertise;</li>
          <li>Make the University’s resources and expertise available to local, regional, national, and international levels for students, researchers, policymakers, practitioners, partner communities, and other stakeholders in agriculture, fisheries, business, education, social sciences and related fields; and,</li>
          <li>Sustain its growth and development by generating increased funding from a variety of external sources, and harnessing partnership and collaboration with industry, government, and non-government organizations.</li>
        </ul>

        <h4>Mode of Delivery</h4>
        <p>
          Distance education is a mode of education delivery whereby teacher and learner are separated in time and space, and instruction is delivered through specially designed materials and methods using appropriate technologies, and supported by organizational and administrative structures and arrangements.
        </p>

        <h4>Learning Materials</h4>
        <p>
          The Open University students are provided with specially packaged printed instructional materials or self-learning modules which they study on their own most of the time. SLMs are prepared by a "quality circle" composed of a curriculum designer, who develops the subject based on the approved syllabus; subject matter specialist, who writes the module; a content critic; a language editor; and a media specialist or graphics artist.
        </p>

      </div>
    </div>

  </div>
</section>


@endsection
