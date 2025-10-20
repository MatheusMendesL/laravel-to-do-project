@extends('templates/main_layout')

@section('content')
    <div class="container">
        <div class="row mt-5">
            <div class="col">

                <h4>Editar tarefa</h4>
                <hr>
                <form action="{{ route('edit_task_submit') }}" method="post">
                    @csrf
                    <input type="hidden" name="task_id" value="{{ Crypt::encrypt($task->id) }}">
                    <div class="mb-3">
                        <label for="text_task_name" class="form-label">Nome da tarefa</label>
                        <input type="text" name="text_task_name" id="text_task_name" class="form-control"
                            placeholder="Nome da tarefa" required value="{{ old('text_task_name', $task->task_name) }}">
                        @error('text_task_name')
                            <div class="text-danger">
                                {{ $errors->get('text_task_name')[0] }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="text_task_description" class="form-label">Descrição da tarefa</label>
                        <textarea name="text_task_description" id="text_task_description" class="form-control" cols="30" rows="5"
                            required>{{ old('text_task_description', $task->task_description) }}</textarea>
                        @error('text_task_description')
                            <div class="text-danger">
                                {{ $errors->get('text_task_description')[0] }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="text_task_status" class="form-label">Status da tarefa</label>
                        <select name="text_task_status" id="text_task_status" class="form-select w-25">
                            <option value="new"
                                {{ old('text_task_status', $task->task_status == 'new' ? 'select' : '') }}>Nova</option>
                            <option value="in_progress"
                                {{ old('text_task_status', $task->task_status == 'in_progress' ? 'select' : '') }}>Em
                                progresso</option>
                            <option value="cancelled"
                                {{ old('text_task_status', $task->task_status == 'cancelled' ? 'select' : '') }}>Cancelada
                            </option>
                            <option value="completed"
                                {{ old('text_task_status', $task->task_status == 'completed' ? 'select' : '') }}>Concluída
                            </option>
                        </select>
                    </div>

                    <div class="mb-3 text-center">
                        <a href="{{ route('index') }}" class="btn btn-dark px-5"><i class="bi bi-x-circle me-2"></i>
                            Voltar</a>
                        <button type="submit" class="btn btn-secondary px-5 m-1"><i class="bi bi-floppy me-2"></i>
                            Salvar</button>
                    </div>
                </form>

                @if (session()->has('task_error'))
                    <div class="alert alert-danger text-center p-1">
                        {{ session()->get('task_error') }}
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
