<div class="row mt-5">

    <h3>Total Checked proxies: {{count($workingProxies)}}</h3>
    <h3>Working proxies: {{count($workingProxies)}}</h3>
    <h3>Not working proxies: {{count($notWorkingProxies)}}</h3>
    <table class="table table-striped">
        <thead class="table-dark">
        <tr>
            <th scope="col">IP:PORT</th>
            <th scope="col">Proxy Type</th>
            <th scope="col">Download Speed</th>
            <th scope="col">Status</th>
        </tr>
        </thead>
        <tbody class="">
        @foreach($workingProxies as $workingProxy)
            <tr>
                <th scope="row">{{$workingProxy['ip'].':'.$workingProxy['port']}}</th>
                <td>{{$workingProxy['type']}}</td>
                <td>{{$workingProxy['downloadSpeed']}} ms</td>
                <td>{{$workingProxy['status']}}</td>
            </tr>
        @endforeach

        @foreach($notWorkingProxies as $notWorkingProxy)
            <tr>
                <th scope="row">{{$notWorkingProxy['ip'].':'.$notWorkingProxy['port']}}</th>
                <td>{{''}}</td>
                <td>{{''}}</td>
                <td>{{$notWorkingProxy['status']}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
