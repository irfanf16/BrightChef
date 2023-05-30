<form method="POST" action="/api/user">
    @csrf
    <div class="form-group row">
        <label for="username"
               class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

        <div class="col-md-6">
            <input id="username" type="text"
                   class="form-control"
                   name="username" value="{{ old('username') }}" required autofocus>
        </div>
    </div>
    <div class="form-group row mb-0">
        <div class="col-md-8 offset-md-4">
            <button type="submit" class="btn btn-primary">
                {{ __('Create') }}
            </button>
        </div>
    </div>
</form>
