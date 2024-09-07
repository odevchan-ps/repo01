<!-- ページ情報を表示 -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="page-info">
        @if ($items->count())
            Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of {{ $items->total() }} entries
        @else
            No entries available.
        @endif
    </div>
    <a href="{{ $createRoute }}" class="btn btn-primary">Add New</a>
</div>

<!-- ページネーションリンク -->
<div class="d-flex justify-content-center mb-3">
    {{ $items->links('vendor.pagination.custom') }}
</div>
