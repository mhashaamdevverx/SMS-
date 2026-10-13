@extends('layout')

@section('content')

@php
    $fmt = fn ($n) => rtrim(rtrim(number_format((float) $n, 2, '.', ''), '0'), '.');
@endphp

<div class="form-container class-result-container">

    <div class="form-box class-result-box">

        {{-- Header --}}
        <div class="class-result-header">

            <div class="class-result-title">
                <span><i class="bi bi-clipboard2-check-fill" aria-hidden="true"></i></span>

                <div>
                    <h2>Class Results</h2>
                    <p>{{ $currentUser->role?->slug === 'class-incharge' ? 'Add marks for students in your assigned class.' : 'Select a student to enter or update obtained marks for their subjects.' }}</p>
                </div>
            </div>

            <a href="{{ route('results.add') }}" class="class-result-back">
                <i class="bi bi-arrow-left"></i>
                Back
            </a>

        </div>

        {{-- Chips --}}
        <div class="class-result-chips">

            <div class="class-result-chip">
                <i class="bi bi-mortarboard-fill"></i>
                Selected Class: <strong>{{ $selectedClass }}</strong>
            </div>

            <div class="class-result-chip ext-style-156">
                <i class="bi bi-award"></i>
                Exam Term: <strong>{{ $selectedTerm }}</strong>
            </div>

            <div class="class-result-chip">
                <i class="bi bi-people-fill"></i>
                Students: <strong>{{ $students->count() }}</strong>
            </div>

        </div>

        {{-- Success alert --}}
        @if(session('success'))
            <div class="class-result-alert">
                <i class="bi bi-check-circle-fill"></i>
                {{ session('success') }}
            </div>
        @endif

        {{-- Errors alert --}}
        @if($errors->any())
            <div class="class-result-error">
                <strong>
                    <i class="bi bi-exclamation-circle-fill"></i>
                    Please fix the following:
                </strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Main Table with 2 Columns: STUDENT & ACTION --}}
        <div class="class-result-table-wrapper">

            <table class="class-result-table class-result-table-2col">

                <thead>
                    <tr>
                        <th class="ext-style-157">STUDENT</th>
                        <th class="ext-style-158">ACTION</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($students as $student)

                    @php
                        $enteredCount = $student->class_subjects->whereNotNull('obtained_marks')->count();
                        $totalSubjectsCount = $student->class_subjects->count();
                    @endphp

                    <tr>

                        {{-- Column 1: STUDENT --}}
                        <td class="student-cell ext-style-159">
                            <div class="student-info-main">
                                <span class="student-name ext-style-160">
                                    {{ $student->name }}
                                </span>

                                <div class="student-meta ext-style-161">
                                    Section: <strong class="ext-style-162">{{ $student->section ?? 'N/A' }}</strong> · 
                                    Class: <strong class="ext-style-162">{{ $student->class }}</strong> · 
                                    Roll No: <strong class="ext-style-152">{{ $student->std_id ?? $student->id }}</strong>
                                </div>
                            </div>
                        </td>

                        {{-- Column 2: ACTION --}}
                        <td class="ext-style-163">
                            @if($currentUser->hasPermission('results.create') && ($currentUser->isAdmin() || $enteredCount === 0))
                                <button type="button" class="add-result-btn ext-style-164" data-crud-modal-open="student-result-modal-{{ $student->id }}">
                                    <i class="bi bi-plus-circle-fill"></i>
                                    {{ $enteredCount > 0 ? 'Edit Result' : 'Add Result' }}
                                </button>
                            @elseif($currentUser->role?->slug === 'class-incharge' && $enteredCount > 0)
                                <span class="class-result-chip">Result already recorded</span>
                            @endif
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="2" class="class-result-empty ext-style-165">
                            No students found for this class.
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- POPUP MODAL FOR EACH STUDENT (3 COLUMNS INSIDE) --}}
@foreach($students as $student)
    <div class="crud-backdrop" id="student-result-modal-{{ $student->id }}" hidden>
        <section class="crud-modal" role="dialog" aria-modal="true" class="ext-style-166">
            <button type="button" class="modal-close" data-crud-modal-close aria-label="Close dialog">&times;</button>

            <div class="ext-style-167">
                <div class="ext-style-168">
                    <i class="bi bi-clipboard2-plus-fill"></i>
                </div>
                <div>
                    <h3 class="ext-style-169">Add Result Marks ({{ $selectedTerm }})</h3>
                    <p class="ext-style-170">
                        Student: <strong class="ext-style-152">{{ $student->name }}</strong> · Class <strong>{{ $student->class }} {{ $student->section ? '('.$student->section.')' : '' }}</strong> · Roll No: <strong>{{ $student->std_id ?? $student->id }}</strong>
                    </p>
                </div>
            </div>

            <form action="{{ route('results.student.store') }}" method="POST">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">
                <input type="hidden" name="student_class" value="{{ $selectedClass }}">
                <input type="hidden" name="term" value="{{ $selectedTerm }}">

                {{-- Modal Table with 3 Columns: SUBJECT | TOTAL MARKS | OBTAINED MARKS --}}
                <div class="ext-style-171">
                    <table class="modal-result-table ext-style-172">
                        <thead>
                            <tr class="ext-style-173">
                                <th class="ext-style-174">SUBJECT</th>
                                <th class="ext-style-175">TOTAL MARKS</th>
                                <th class="ext-style-176">OBTAINED MARKS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student->class_subjects as $sub)
                                <tr class="ext-style-177">
                                    {{-- Column 1: SUBJECT --}}
                                    <td class="ext-style-178">
                                        {{ $sub->subject_name }}
                                    </td>

                                    {{-- Column 2: TOTAL MARKS --}}
                                    <td class="ext-style-179">
                                        <span class="ext-style-180">
                                            {{ $fmt($sub->total_marks) }}
                                        </span>
                                    </td>

                                    {{-- Column 3: OBTAINED MARKS --}}
                                    <td class="ext-style-181">
                                        <div class="ext-style-182">
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0" 
                                                   max="{{ $sub->total_marks }}" 
                                                   name="marks[{{ $sub->subject_name }}]" 
                                                   value="{{ $sub->obtained_marks !== null ? $fmt($sub->obtained_marks) : '' }}"
                                                   placeholder="Marks"
                                                   class="ext-style-183">
                                            <span class="ext-style-184">/ {{ $fmt($sub->total_marks) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="ext-style-185">
                                        No subjects declared for this class in the Subjects section.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Modal Footer Buttons --}}
                <div class="ext-style-186">
                    <button type="submit" class="modal-primary-btn ext-style-187">
                        <i class="bi bi-check2-circle ext-style-188"></i> Save Result
                    </button>
                    <button type="button" class="modal-secondary-btn" data-crud-modal-close class="ext-style-189">
                        Cancel
                    </button>
                </div>
            </form>
        </section>
    </div>
@endforeach

@endsection