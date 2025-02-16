@if(@$listData->count() > 0)
    @foreach ($listData as $data)
        <tr>
            <td><input type="checkbox" class="select-item" value="{{ @$data->id }}"></td>
            <td>{{ @$data->id }}</td>
            <td>{{ @$data->name }}</td>
            <td>{{ @$data->lastName }}</td>
            <td>{{ @$data->phone }}</td>
            <td>
                <a class="text-danger delete-contact" href="javascript:void(0);" data-id="{{ @$data->id }}">
                    <i class="fa fa-trash"></i> Delete
                </a>
                &nbsp;
                <a class="text-primary edit-contact" href="javascript:void(0);" data-id="{{ @$data->id }}">
                    <i class="fa fa-edit"></i> Edit
                </a>
            </td>
        </tr>
    @endforeach
    <tr>
        <td colspan="6">
            {!! @$listData->links('vendor.pagination.bootstrap-5') !!}
        </td>
    </tr>
    <input type="hidden" name="page" id="hidden_page" value="{{@$listData->currentPage()}}"/>
@else
    <tr class="text-center">
        <td colspan="6">
            No Data Found
        </td>
    </tr>
@endif
