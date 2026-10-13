@extends('layout')

@section('content')
<div class="container results-page">
    <div class="student-header results-header">
        <div>
            <p class="results-kicker">Academic records</p>
            <h1>Results</h1>
        </div>
        @if($currentUser->hasPermission('results.create'))<a href="{{ route('results.add') }}" class="student-add-btn"><i class="bi bi-plus-lg" aria-hidden="true"></i> Add Result</a>@endif
    </div>

    @if(session('success'))
        <div class="success auto-hide-message">{{ session('success') }}</div>
    @endif

    {{-- DUAL FILTER BAR: EXAM TERM & CLASS/SECTION --}}
    <div class="results-filter-card ext-style-100">
        <form action="/results" method="GET" id="resultsFilterForm" class="ext-style-101">
            
            {{-- FILTER 1: EXAM TERM --}}
            <div class="ext-style-102">
                <label class="ext-style-103">
                    <i class="bi bi-calendar2-event ext-style-104"></i> Exam Term
                </label>
                <div class="input-wrapper">
                    <select name="term" class="form-control" onchange="document.getElementById('resultsFilterForm').submit()">
                        <option value="">All Terms</option>
                        @if(isset($availableTerms))
                            @foreach($availableTerms as $t)
                                <option value="{{ $t }}" {{ $selectedTerm == $t ? 'selected' : '' }}>
                                    {{ $t }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            {{-- FILTER 2: CLASS & SECTION --}}
            <div class="ext-style-105">
                <label class="ext-style-103">
                    <i class="bi bi-mortarboard-fill ext-style-104"></i> Class & Section
                </label>
                <div class="input-wrapper">
                    <select name="class_section" class="form-control" onchange="document.getElementById('resultsFilterForm').submit()">
                        <option value="">All Classes</option>
                        @if(isset($classOptions))
                            @foreach($classOptions as $clsOpt)
                                <option value="{{ $clsOpt }}" {{ $selectedClass == $clsOpt ? 'selected' : '' }}>
                                    {{ $clsOpt }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            {{-- RESET FILTERS BUTTON --}}
            @if(!empty($selectedTerm) || !empty($selectedClass))
                <div>
                    <a href="/results" class="ext-style-106">
                        <i class="bi bi-x-circle-fill"></i> Reset Filters
                    </a>
                </div>
            @endif
        </form>
    </div>

    <div class="unified-table-card"><div class="table-responsive-wrapper">
        <table class="student-table results-table unified-table">
            <thead>
                <tr>
                    <th>Std. Name</th>
                    <th>Std. Section</th>
                    <th>Std. Class</th>
                    <th>Exam Term</th>
                    <th>All Subjects</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($studentResults as $groupIndex => $studentResult)
                    <tr>
                        <td><strong>{{ $studentResult['student_name'] }}</strong></td>
                        <td>{{ $studentResult['student_section'] }}</td>
                        <td>{{ $studentResult['student_class'] }}</td>
                        <td>
                            <span class="badge ext-style-107">
                                <i class="bi bi-award"></i> {{ $studentResult['term'] ?? 'First Term' }}
                            </span>
                        </td>
                        <td>
                            <div class="result-subject-list">
                                @foreach($studentResult['results'] as $result)
                                    <span>{{ $result->subject }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="table-actions result-actions">
                            <button type="button" class="icon-action view-action" data-crud-modal-open="student-result-view-{{ $groupIndex }}" title="View all subject results" aria-label="View all subject results"><i class="bi bi-eye-fill" aria-hidden="true"></i></button>
                            @if($currentUser->hasPermission('results.update'))
                                <button type="button" class="icon-action edit-action" data-crud-modal-open="student-result-edit-{{ $groupIndex }}" title="Edit all subject results" aria-label="Edit all subject results"><i class="bi bi-pencil-square" aria-hidden="true"></i></button>
                            @endif
                            @if($currentUser->hasPermission('results.delete'))
                                <form action="{{ route('results.bulk-destroy') }}" method="POST" data-crud-action="delete">
                                    @csrf @method('DELETE')
                                    @foreach($studentResult['results'] as $result)<input type="hidden" name="result_ids[]" value="{{ $result->id }}">@endforeach
                                    <button type="submit" class="icon-action delete-action" title="Delete all student results" aria-label="Delete all student results"><i class="bi bi-trash3" aria-hidden="true"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="results-empty"><i class="bi bi-clipboard2-x" aria-hidden="true"></i><span>No results saved yet for the selected filter.</span></td></tr>
                @endforelse
            </tbody>
        </table></div></div>

    {{-- PAGINATION --}}
    @if($studentResults->total() > 0)
        <div class="flex-between-center-mt18-wrap" style="display: flex; align-items: center; justify-content: space-between; margin-top: 18px; flex-wrap: wrap; gap: 12px;">
            <p class="student-pagination-info m-0" style="color: #64748b; font-size: 13px; font-weight: 600;">
                Showing {{ $studentResults->firstItem() }} to {{ $studentResults->lastItem() }} of {{ $studentResults->total() }} results
            </p>

            @if($studentResults->hasPages())
                <div class="student-pagination">
                    {{ $studentResults->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    @endif

    @foreach($studentResults as $groupIndex => $studentResult)
        <div class="crud-backdrop" id="student-result-view-{{ $groupIndex }}" hidden>
            <section class="crud-modal result-view-modal result-group-modal" role="dialog" aria-modal="true" aria-labelledby="student-result-view-title-{{ $groupIndex }}">
                <button type="button" class="modal-close" data-crud-modal-close aria-label="Close result details">&times;</button>
                <div class="result-modal-heading">
                    <span class="result-modal-icon"><i class="bi bi-clipboard2-check-fill" aria-hidden="true"></i></span>
                    <div>
                        <p>{{ $studentResult['term'] ?? 'First Term' }} — All subject results</p>
                        <h2 id="student-result-view-title-{{ $groupIndex }}">{{ $studentResult['student_name'] }}</h2>
                    </div>
                </div>
                <div class="result-summary-grid">
                    <div><span>Section</span><strong>{{ $studentResult['student_section'] }}</strong></div>
                    <div><span>Class</span><strong>{{ $studentResult['student_class'] }}</strong></div>
                    <div><span>Exam Term</span><strong>{{ $studentResult['term'] ?? 'First Term' }}</strong></div>
                    <div><span>Total Marks</span><strong>{{ rtrim(rtrim(number_format($studentResult['total_marks'], 2), '0'), '.') }}</strong></div>
                    <div><span>Obtained Marks</span><strong>{{ rtrim(rtrim(number_format($studentResult['obtained_marks'], 2), '0'), '.') }}</strong></div>
                    <div><span>Overall Percentage</span><strong>{{ number_format($studentResult['percentage'], 2) }}%</strong></div>
                </div>
                <div class="result-subjects-table-wrap">
                    <table class="result-subjects-table">
                        <thead><tr><th>Subject</th><th>Total</th><th>Obtained</th><th>Percentage</th><th>Grade</th></tr></thead>
                        <tbody>@foreach($studentResult['results'] as $result)<tr><td>{{ $result->subject }}</td><td>{{ rtrim(rtrim(number_format($result->total_marks, 2), '0'), '.') }}</td><td>{{ rtrim(rtrim(number_format($result->obtained_marks, 2), '0'), '.') }}</td><td>{{ number_format($result->percentage, 2) }}%</td><td>{{ $result->grade ?? '-' }}</td></tr>@endforeach</tbody>
                    </table>
                </div>
            </section>
        </div>

        @if($currentUser->hasPermission('results.update'))
        <div class="crud-backdrop" id="student-result-edit-{{ $groupIndex }}" hidden>
            <section class="crud-modal result-edit-modal result-group-modal" role="dialog" aria-modal="true" aria-labelledby="student-result-edit-title-{{ $groupIndex }}">
                <button type="button" class="modal-close" data-crud-modal-close aria-label="Close result edit dialog">&times;</button>
                <h2 id="student-result-edit-title-{{ $groupIndex }}">Edit Results: {{ $studentResult['student_name'] }} ({{ $studentResult['term'] ?? 'First Term' }})</h2>
                <form action="{{ route('results.bulk-update') }}" method="POST" data-crud-action="update" class="group-result-edit-form">
                    @csrf @method('PUT')
                    <div class="result-subjects-table-wrap">
                        <table class="result-subjects-table">
                            <thead><tr><th>Subject</th><th>Total</th><th>Obtained</th><th>Percentage</th><th>Grade</th></tr></thead>
                            <tbody>
                                @foreach($studentResult['results'] as $resultIndex => $result)
                                    <tr data-total="{{ $result->total_marks }}">
                                        <td><input type="hidden" name="results[{{ $resultIndex }}][id]" value="{{ $result->id }}">{{ $result->subject }}</td>
                                        <td>{{ rtrim(rtrim(number_format($result->total_marks, 2), '0'), '.') }}</td>
                                        <td><input type="number" name="results[{{ $resultIndex }}][obtained_marks]" value="{{ $result->obtained_marks }}" min="0" max="{{ $result->total_marks }}" step="0.01" data-edit-obtained required></td>
                                        <td data-edit-percentage>{{ number_format($result->percentage, 2) }}%</td>
                                        <td data-edit-grade>{{ $result->grade ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr><th>Overall</th><th data-edit-total>{{ rtrim(rtrim(number_format($studentResult['total_marks'], 2), '0'), '.') }}</th><th data-edit-obtained-total>{{ rtrim(rtrim(number_format($studentResult['obtained_marks'], 2), '0'), '.') }}</th><th data-edit-overall-percentage>{{ number_format($studentResult['percentage'], 2) }}%</th><th data-edit-overall-grade>{{ $studentResult['grade'] }}</th></tr>
                            </tfoot>
                        </table>
                    </div>
                    @error('results')<span class="crud-field-error">{{ $message }}</span>@enderror
                    <button type="submit" class="modal-primary-btn">Update All Results</button>
                </form>
            </section>
        </div>
        @endif
    @endforeach
</div>
@endsection