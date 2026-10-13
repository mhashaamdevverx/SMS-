@extends('layout')

@section('content')

<div class="form-container result-form-container">
    <div class="form-box result-form-box">

        <div class="result-form-title">
            <span>
                <i class="bi bi-clipboard2-plus-fill" aria-hidden="true"></i>
            </span>

            <div>
                <h2>Add New Result</h2>
                <p>Select Exam Term and Class &amp; Section to load all students and their subjects.</p>
            </div>
        </div>

        <form action="{{ route('results.class') }}" method="GET" id="classResultForm" class="result-class-form" data-result-students="{{ json_encode($students) }}" data-class-options="{{ json_encode($classOptions) }}">

            <div class="result-selection-fields">

                {{-- EXAM TERM SELECT --}}
                <div class="field-group ext-style-190">
                    <label for="result-term">Exam Term</label>
                    <div class="input-wrapper">
                        <select id="result-term" name="term" class="form-control ext-style-191">
                            @if(isset($availableTerms))
                                @foreach($availableTerms as $t)
                                    <option value="{{ $t }}">{{ $t }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                {{-- CLASS & SECTION SELECT --}}
                <div class="field-group result-class-field">

                    <label for="result-class">Std. Class &amp; Section</label>

                    <div class="result-combobox">

                        <input
                            id="result-class"
                            type="text"
                            name="class"
                            value=""
                            placeholder="Search and select a class & section"
                            autocomplete="off"
                            role="combobox"
                            aria-autocomplete="list"
                            aria-expanded="false"
                            aria-controls="result-class-dropdown-menu"
                        >

                        <button
                            type="button"
                            class="result-combobox-toggle"
                            id="classToggle"
                            aria-label="Show classes"
                        >
                            <i class="bi bi-chevron-down"></i>
                        </button>

                        <div
                            id="result-class-dropdown-menu"
                            class="result-combobox-menu"
                            role="listbox"
                            hidden
                        ></div>

                    </div>

                    <div class="class-help-text">
                        Select a class and section to continue and view all students.
                    </div>

                </div>

            </div>

            <button
                type="submit"
                class="next-class-btn"
                id="nextClassButton"
                disabled
            >
                <i class="bi bi-arrow-right-circle-fill"></i>
                Next
            </button>

        </form>

    </div>
</div>

@endsection