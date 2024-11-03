@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-[#10163f] focus:ring-[#10163f] rounded-md shadow-sm']) !!}>