{{-- <div>honey, im home!</div> --}}

<x-layout>
    <x-slot:title>Your Profile</x-slot:title>

    @auth
        @include('profile.home')
    @else
        @include('profile.home_guest')
    @endauth
</x-layout>