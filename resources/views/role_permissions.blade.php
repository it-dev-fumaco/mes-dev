<form action="/save_role_permissions/{{ $user_group }}" method="POST" autocomplete="off" id="role-permission-form">
    @csrf
    <div class="row border p-0 m-0">
        @foreach ($actions as $type => $action_list)
        @php
            $slug_type = str_slug($type);
        @endphp
        <div class="col-6 p-3">
            <table class="table table-bordered table-striped table-hover m-0">
                <col style="width: 79%;">
                <col style="width: 21%;">
                <thead class="text-white" style="font-size: 11px; background-color:#EB984E;">
                    <th class="p-1 font-weight-bolder text-uppercase">{{ $type }}</th>
                    <th class="p-1 font-weight-bolder">
                        <span class="d-block text-uppercase">Is Allowed</span>
                        <small class="d-block" style="font-size: 10px; font-weight: 800;">
                            <a href="#" class="check-all-permission" style="color: #566573;" data-type="{{ $slug_type }}">Check All</a>
                            <span class="text-light">|</span>
                            <a href="#" class="uncheck-all-permission" style="color: #566573;" data-type="{{ $slug_type }}">Uncheck All</a>
                        </small>
                    </th>
                </thead>
                <tbody style="font-size: 13px;">
                    @foreach ($action_list as $action => $description)
                    <tr>
                        <td class="text-left p-2">» {{ $description }}</td>
                        <td class="align-middle p-1">
                            <input type="checkbox" class="{{ $slug_type }}" value="1" name="permission[{{ $action }}]" {{ in_array($action, $existing_permissions) ? 'checked' : '' }}>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endforeach
    </div>
</form>