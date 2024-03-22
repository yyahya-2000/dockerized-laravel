@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Checking proxy servers</h5>
                    <!-- Laravel Form -->
                    <form id="checkProxyForm" method="POST" action="{{ route('proxy.check') }}"
                          onsubmit="disableSubmitButton()">
                        @csrf
                        <div class="form-group">
                            <label for="proxiesTextarea">
                                Enter the list of proxy servers you want to check (up to 1000 servers):
                            </label>
                            <textarea class="form-control" id="proxiesTextarea" name="proxies" rows="5"
                                      placeholder="ip:port&#10;ip:port&#10;ip:port&#10;...">{{ old('proxies') ?? $proxies ?? '' }}</textarea>
                            @error('proxies')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <button id="submitButton" type="submit" class="btn btn-primary mt-3">Check Servers</button>
                    </form>
                </div>
            </div>
        </div>

        @if(isset($workingProxies) || isset($notWorkingProxies))
            @include('proxy.check_result', [
                'workingProxies' => $workingProxies,
                'notWorkingProxies'=>$notWorkingProxies
            ])
        @endif
    </div>
@endsection

<script>
    function disableSubmitButton() {
        document.getElementById('submitButton').disabled = true;
    }
</script>
