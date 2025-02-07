<!DOCTYPE html>
<html>

<head>
    <title>Lista de Colaboradores</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

</head>

<body class="bg-gray-200">
    <a href="{{ url('/') }}">
        <button class="bg-sky-500 my-2 mx-2 text-white text-black px-3 py-2 h-10" type="submit">Home</button>
    </a>

    <a href="{{ url('/colaboradors') }}">
        <button class="bg-sky-500 my-2 mx-2 text-white text-black px-3 py-2 h-10" type="submit">Colaborador</button>
    </a>

    <div
        class="flex justify-center  items-center content-center flex-col align-items-center min-h-screen border-2 border-black">
        <div class="w-1/4">
            <div class="flex justify-between w-full">
                <h1 class="text-xl font-bold">Lista de Colaboradores</h1>
                <a class="ms-3 bg-sky-500 text-white text-black px-3 py-2"
                    href="{{ route('colaboradors.create') }}">Criar</a>

            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>CPF</th>
                        <th>Unidade</th>
                        <th>Data de Criação</th>
                        <th>Última Atualização</th>
                        <th width="280px">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($colaboradors as $colaborador)
                        <tr>
                            <td>{{ $colaborador->id }}</td>
                            <td>{{ $colaborador->nome }}</td>
                            <td>{{ $colaborador->email }}</td>
                            <td>{{ $colaborador->cpf }}</td>
                            <td>{{ $colaborador->unidade_id }}</td>
                            <td>{{ $colaborador->created_at }}</td>
                            <td>{{ $colaborador->updated_at }}</td>
                            <td>
                                <a class="ms-3 bg-sky-500 text-white text-black px-3 py-2 h-10"
                                    href="{{ route('colaboradors.edit', $colaborador->id) }}">Editar</a>
                                <form method="post" class="pt-4s"
                                    action="{{ route('colaboradors.destroy', $colaborador->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="ms-3  bg-red-500 text-white text-black place-self-center  px-3 py-2">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>