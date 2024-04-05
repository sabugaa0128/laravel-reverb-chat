@props([
    'disabled' => false,
    'options' => [],
    'selected' => null,
    'text' => '- Select -',
])

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mb-1']) !!}>
    <option value="" {{ empty($selected) ? 'selected' : '' }} disabled>{{ $text }}</option>
    @if($options)
        @foreach($options as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    @endif
</select>
