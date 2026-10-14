@extends('layout')

@section('content')

<div class="form-container">
    <div class="form-box">
        <h2>Add New Section</h2>

        <form action="/add-section" method="POST">
            @csrf

            <div class="field-group field-full">
                <label for="section-name">Section Name <span style="color: #EF4444; font-weight: 700;">*</span></label>
                <div class="input-wrapper">
                    <input id="section-name" type="text" name="section_name" placeholder="e.g. A, B, Green, Rose" value="{{ old('section_name') }}" class="@error('section_name') input-error @enderror" required>
                    <span class="field-error @error('section_name') has-error @enderror">@error('section_name'){{ $message }}@enderror</span>
                </div>
            </div>

            <div class="form-actions-row" style="display: flex; align-items: center; gap: 12px; margin-top: 20px;">
                <button type="submit" class="add-btn" style="flex: 1;">Save Section</button>
                <a href="/sections" class="btn btn-light" style="padding: 10px 18px; border-radius: var(--radius-md, 8px); border: 1px solid #cbd5e1; font-weight: 600; text-decoration: none; color: #475569;">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection