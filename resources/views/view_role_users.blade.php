<table class="table table-bordered table-striped table-hover m-0">
    <thead class="bg-secondary text-white text-uppercase text-center font-weight-bolder" style="font-size: 10px;">
        <th class="p-2">Module</th>
        <th class="p-2">Operation</th>
        <th class="p-2">Employee Name</th>
    </thead>
    <tbody style="font-size: 11px;">
        @forelse ($users as $user)
        <tr>
            <td class="p-2 text-center">{{ $user->module }}</td>
            <td class="p-2 text-center">{{ $user->operations }}</td>
            <td class="p-2 text-center">{{ $user->employee_name }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="3" class="text-muted text-uppercase text-center">No users found.</td>
        </tr>
        @endforelse
    </tbody>
</table>