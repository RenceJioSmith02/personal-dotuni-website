@extends('layouts.website')

@section('title', 'Course View | CLSU DOT-Uni')

@push('css')

<style>
    .course-section {
  height: 100vh;
  overflow: hidden;
  background: #f2f2f2;
}

.course-container {
  display: flex;
  height: 100%;
}

/* LEFT */
.course-left {
  width: 30%;
}

.sticky-wrapper {
  position: sticky;
  top: 0;
  height: 100vh;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding: 40px;
}

.sticky-wrapper img {
  max-width: 100%;
  height: auto;
}

/* RIGHT */
.course-right {
  width: 70%;
  height: 100vh;
  overflow-y: auto;
  padding: 60px 50px;
}

.course-intro {
  margin-bottom: 30px;
  text-align: justify;
  line-height: 1.7;
}

/* BLOCK */
.info-block {
  margin-bottom: 40px;
  border-left: 3px solid #1c7c34;
  padding-left: 15px;
}

.info-block h4 {
  margin-bottom: 15px;
  text-align: center
}

/* TABLE */
.course-table {
  width: 100%;
  border-collapse: collapse;
}

.course-table th,
.course-table td {
  padding: 10px;
  border-bottom: 1px solid #ccc;
}

.course-table th {
  text-align: left;
}

.total-row {
  font-weight: bold;
}

/* TABLET */
@media (max-width: 992px) {
  .course-left {
    width: 45%;
  }
  .course-right {
    width: 55%;
    padding: 40px 30px;
  }
}

/* MOBILE */
@media (max-width: 768px) {

  .course-section {
    height: auto;
    overflow: visible;
  }

  .course-container {
    flex-direction: column;
  }

  .course-left,
  .course-right {
    width: 100%;
    height: auto;
  }

  .sticky-wrapper {
    position: relative;
    height: auto;
    padding: 20px;
  }

  .course-right {
    overflow: visible;
    padding: 30px 20px;
  }

}

</style>

@endpush

@section('content')

<section class="course-section">
    <div class="course-container">

        <!-- LEFT : STICKY COURSE IMAGE -->
        <div class="course-left">
            <div class="sticky-wrapper">
                <img src="{{ $program->image_url }}" alt="{{ $program->title }}">
            </div>
        </div>

        <!-- RIGHT : COURSE DETAILS -->
        <div class="course-right">
            <div class="course-content">

                <div class="divider"></div>
                <h2 class="section-title">
                    {{ $program->title }}
                </h2>

                <p class="course-intro">
                    {{ $program->description }}
                </p>

                <!-- COURSE REQUIREMENTS -->
                <div class="info-block">
                    <h4>Course Requirements</h4>

                    <table class="course-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Required Units</th>
                            </tr>
                        <tbody>

                        @php
                        $totalUnits = 0;
                        @endphp

                        @forelse($program->requirements as $req)

                        <tr>
                            <td>{{ $req->category->name }}</td>
                            <td>{{ $req->required_units }}</td>
                        </tr>

                        @php
                        $totalUnits += $req->required_units;
                        @endphp

                        @empty

                        <tr>
                            <td colspan="2">No requirements available.</td>
                        </tr>

                        @endforelse

                        <tr class="total-row">
                            <td>TOTAL</td>
                            <td>{{ $totalUnits }}</td>
                        </tr>

                        </tbody>
                    </table>
                </div>

                <!-- PROGRAM STRUCTURE -->
                <div class="info-block">
                    <h4>Program Structure</h4>

                    <table class="course-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>Units</th>
                            </tr>
                        </thead>
                        <tbody>

                            @php
                            $groupedCourses = $program->programCourses->groupBy('requirement_category_id');
                            @endphp

                            @forelse($groupedCourses as $categoryId => $courses)

                                <tr>
                                    <td colspan="3" style="font-weight: bold; padding-top:20px;">
                                        {{ optional($courses->first()->category)->name }}
                                    </td>
                                </tr>

                                @foreach($courses as $pc)
                                <tr>
                                    <td>{{ $pc->course->code }}</td>
                                    <td>{{ $pc->course->title }}</td>
                                    <td>{{ $pc->course->units }}</td>
                                </tr>
                                @endforeach

                            @empty

                            <tr>
                                <td colspan="3">No courses added.</td>
                            </tr>

                            @endforelse

                        </tbody>
                    </table>
                </div>

                <!-- COURSE DESCRIPTION -->
                <div class="info-block">
                    <h4>Course Description</h4>

                    @forelse($program->programCourses as $pc)

                        <h5>
                            {{ $pc->course->code }}
                            {{ strtoupper($pc->course->title) }}
                        </h5>

                        <p>
                            {{ $pc->course->description }}
                        </p>
                        <span style="color: darkcyan;">{{ $pc->course->units }} Units</span>

                        @empty

                        <p>No course descriptions available.</p>

                    @endforelse

                </div>

            </div>
        </div>

    </div>
</section>

@endsection
