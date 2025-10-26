@extends('admin.layouts.admin')

@section('content')
    <div class="container mt-4">
        <h3>📚 Loại vi phạm</h3>
        <a href="{{ route('loaivipham.create') }}" class="btn btn-success mb-3">+ Thêm loại</a>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Tên</th>
                            <th>Mô tả</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($types as $t)
                            <tr>
                                <td>{{ $t->code }}</td>
                                <td>{{ $t->name }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($t->description, 60) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('loaivipham.edit', $t->id) }}"
                                        class="btn btn-sm btn-outline-primary">Sửa</a>
                                    <form action="{{ route('loaivipham.destroy', $t->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Xóa loại vi phạm này?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Chưa có dữ liệu</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3 d-flex justify-content-center">
            {{ $types->links() }}
        </div>
    </div>
@endsection
