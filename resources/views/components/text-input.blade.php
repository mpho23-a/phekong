@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-phekong-500 focus:ring-phekong rounded-md shadow-sm']) }}>
