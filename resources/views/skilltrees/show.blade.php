@extends('layouts.app')

@section('content')
    @include('skilltrees.header')
    {{-- <skill-cards
        :skilltree="{{ $skilltree }}"
        :skills="{{ $skilltree->skills }}"></skill-cards> --}}
        <section class="skillzone">
        @can('update', $skilltree)
            <skill-card
                :skilltree="{{ $skilltree }}"
                class="root"
            ></skill-card>
            @foreach ($skilltree->skills as $skill)
                <skill-card
                    :skill="{{ $skill }}"
                ></skill-card>
            @endforeach
        <edit-skill-modal></edit-skill-modal>
        <manage-skilltree-modal
            :skilltree="{{ $skilltree }}"
            :members="{{ $skilltree->members->where('teacher', true) }}">
        </manage-skilltree-modal>
        @elsecan('read', $skilltree)
            <student-skill-card
                :skilltree="{{ $skilltree }}"
                class="root"
            ></student-skill-card>
            @foreach ($skilltree->skills as $skill)
                <student-skill-card
                    :skill="{{ $skill }}"
                ></student-skill-card>
            @endforeach
        @endcan
        </section>
    @include('skilltrees.activity.feed')
@endsection
