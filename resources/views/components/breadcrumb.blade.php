@props(['items'])

<nav class="breadcrumb" aria-label="Breadcrumb">
  @foreach($items as $i => $item)
    @if(!$loop->last)
      <a class="breadcrumb-link" href="{{ $item['url'] }}">{{ $item['label'] }}</a>
      <span class="breadcrumb-sep">›</span>
    @else
      <span class="breadcrumb-current" aria-current="page">{{ $item['label'] }}</span>
    @endif
  @endforeach
</nav>
