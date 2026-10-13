@extends('layout')

@section('content')

<div class="form-container">
    <div class="form-box">
        <h2>Add New Section</h2>

        <form action="/add-section" method="POST">
            @csrf

            <div class="field-group field-full">
                <label for="section-name">Section Name</label>
                <div class="input-wrapper">
                    <input id="section-name" type="text" name="section_name" placeholder="Enter section name" value="{{ old('section_name') }}" class="@error('section_name') input-error @enderror" required>
                    <span class="field-error @error('section_name') has-error @enderror">@error('section_name'){{ $message }}@enderror</span>
                </div>
            </div>

            <button type="submit" class="add-btn">Add</button>
        </form>
    </div>
</div>

@endsection