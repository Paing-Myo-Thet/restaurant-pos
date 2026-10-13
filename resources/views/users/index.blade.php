<x-app-layout>

    <div class='py-6 mx-5'>
        @if(session('success'))
        <div>
            <p class="bg-green-300 px-3 py-6 border-l border-green-600 ">{{session('success')}}</p>
        </div>
        @endif
        @if(session('dl-success'))
        <div>
            <p class="bg-green-300 px-3 my-5 py-2 border-l border-green-600 ">{{session('dl-success')}}</p>
        </div>
        @endif
        <div class="flex mx-3 justify-between items-center">
            <div>
                <h2 class="text-lg font-bolder ">The Users from Restaurant</h2>
            </div>
            <div>
                <a class="bg-blue-400 hover:bg-blue-600 px-6 text-white  rounded  py-3" href="{{route('users.create')}}">Add User</a>
            </div>
        </div>
    </div>
    <div class="m-3 rounded bg-white">
        <table class="w-full ">
            <thead>
                <tr>
                    <th class="text-left px-3 py-5">No</th>
                    <th class="text-left px-3 py-5">Name</th>
                    <th class="text-left px-3 py-5">Email</th>
                    <th class="text-left px-3 py-5">Role</th>
                    <th class="text-left px-3 py-5">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="border-t border-gray-200">
                    <td class="text-left px-3 py-5">{{$loop->iteration}}</td>
                    <td class="text-left px-3 py-5">{{$user->name}}</td>
                    <td class="text-left px-3 py-5">{{$user->email}}</td>
                    <td class="text-left px-3 py-5">{{$user->role}}</td>
                    <td class="flex text-left px-3 py-5 gap-3 items-center">
                        <a href="{{route('users.edit', $user)}}" class="border-b border-blue-300 text-blue-300">Edit</a>
                        <form action="{{route('users.destroy', $user)}}" onsubmit="return confirm('Do you want to delete')" method="Post">
                            @csrf
                            @method('DELETE')
                            <button class="text-white px-6 py-1 bg-red-400 rounded hover:bg-red-500">
                                Delete
                            </button>
                        </form>
                    </td>

                </tr>
                @empty
                <td colspan="5" class="text-center py-3">User Not Found</td>
                @endforelse
            </tbody>
        </table>
    </div>

</x-app-layout>
