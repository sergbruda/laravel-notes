<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Канбан-доска</title>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <style>
        body { font-family: "Segoe UI", Tahoma, sans-serif; background: #f4f7f6; margin: 0; padding: 20px; color: #333; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background: white; padding: 15px 25px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .kanban-board { display: flex; gap: 20px; overflow-x: auto; padding-bottom: 20px; }
        .kanban-column { background: #ebecf0; border-radius: 10px; width: 320px; min-width: 320px; display: flex; flex-direction: column; max-height: calc(100vh - 120px); }
        .col-header { padding: 15px; font-weight: bold; font-size: 1.1em; border-bottom: 2px solid #ccc; display: flex; justify-content: space-between; align-items: center; }
        .col-new .col-header { border-color: #3498db; color: #2980b9; }
        .col-progress .col-header { border-color: #f39c12; color: #e67e22; }
        .col-done .col-header { border-color: #2ecc71; color: #27ae60; }
        .kanban-cards { padding: 10px; flex-grow: 1; overflow-y: auto; min-height: 50px; }
        .kanban-card { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 10px; cursor: grab; transition: transform 0.2s, box-shadow 0.2s; border-left: 4px solid #ccc; }
        .kanban-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.15); }
        .kanban-card:active { cursor: grabbing; transform: rotate(2deg); }
        .card-title { font-weight: bold; margin-bottom: 5px; }
        .card-cat { font-size: 0.8em; background: #eee; padding: 2px 8px; border-radius: 10px; display: inline-block; }
        .sortable-ghost { opacity: 0.4; background: #d1d5db; border: 2px dashed #888; }
        .btn { padding: 10px 15px; color: white; border: none; border-radius: 5px; text-decoration: none; cursor: pointer; }
        .btn-back { background: #95a5a6; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📋 Канбан-доска</h1>
        <a href="/" class="btn btn-back">← Назад к заметкам</a>
    </div>

    <div class="kanban-board">
        @foreach(['new' => 'Новые', 'in_progress' => 'В работе', 'done' => 'Выполнены'] as $status => $title)
        <div class="kanban-column col-{{ str_replace('_', '-', $status) }}" data-status="{{ $status }}">
            <div class="col-header">
                <span>{{ $title }}</span>
                <span style="background:rgba(0,0,0,0.1);padding:2px 8px;border-radius:10px;font-size:0.8em;">{{ $notes[$status]->count() ?? 0 }}</span>
            </div>
            <div class="kanban-cards">
                @if(isset($notes[$status]))
                @foreach($notes[$status] as $note)
                <div class="kanban-card" data-id="{{ $note->id }}">
                    <div class="card-title">{{ $note->title }}</div>
                    @if($note->category)
                        <div class="card-cat">{{ $note->category->name }}</div>
                    @endif
                </div>
                @endforeach
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <script>
        document.querySelectorAll('.kanban-cards').forEach(el => {
            new Sortable(el, {
                group: 'kanban',
                animation: 200,
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                onEnd: function (evt) {
                    const itemEl = evt.item;
                    const newStatus = itemEl.closest('.kanban-column').dataset.status;
                    
                    // Обновляем порядок визуально сразу
                    fetch(`/notes/reorder`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            item_id: itemEl.dataset.id,
                            new_status: newStatus,
                            new_order: evt.newIndex
                        })
                    }).then(res => res.json()).then(data => {
                        if(data.success) location.reload(); // Перезагружаем чтобы обновить счетчики
                    });
                }
            });
        });
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</body>
</html>