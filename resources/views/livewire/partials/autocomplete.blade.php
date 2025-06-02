<div class="input-group" role="menu" x-data="dropdown()">
    <span class="input-group-text">
        <i class="bi bi-search"></i>
        <x-front.livewire-ajax-spinner />
    </span>
    <input class="input-livewire-autocomplete-hidden"
           type="hidden"
           x-model="value"
           name="{{$name}}"
           value="">

    <input class="form-control  dropdown-toggle" role="button"
           wire:model.live.debounce.100ms="input"
           @focus="showDropdown = true"
           @keydown="showDropdown = true"
           @click="showDropdown = true; {{$showResultsOnClick?'$wire.showResults=true;$wire.$refresh()':''}}"
           @keydown.escape.window="showDropdown = false"
           @keydown.arrow-up.prevent="activeIndex = activeIndex > 0 ? activeIndex - 1 : {{ count($options) - 1 }}"
           @keydown.arrow-down.prevent="activeIndex = activeIndex < {{ count($options) - 1 }} ? activeIndex + 1 : 0"
           @if($showResults)
               @keydown.enter.prevent="onEnter"
            @endif
    />
    @if($showResults)
        <div class="dropdown position-absolute mt-5" x-show="showDropdown">
            <ul @class([
        'dropdown-menu' => true,
        'show' => $showResults,
        ])>
                @foreach($options as $v => $label)
                    <li><a x-bind:class="{ 'active': activeIndex === {{ $loop->index }} }"
                           class="dropdown-item"
                           x-ref="option{{ $loop->index }}"
                           @click.prevent="onClicked({{ $loop->index }})"
                           data-id="{{$v}}"
                           href="#">{{$label}}</a></li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('dropdown', () => ({
      activeIndex: 0,
      showDropdown: true,
      value: '{{addcslashes($initialValue, "'")}}',
      input: @entangle('input'),
      init() {
        this.$watch('input', (value) => {
          this.activeIndex = 0;
        });
      },
      onEnter() {
        if (this.activeIndex !== null && this.$refs[`option${this.activeIndex}`]) {
          this.value = this.$refs[`option${this.activeIndex}`].getAttribute('data-id');
          this.showDropdown = false;
          let payload = {
            id: this.$el.closest('[wire\\:id]').getAttribute('wire:id'),
            value: this.value,
          };
          Livewire.dispatch('selectedOption', payload);
        }
      },
      onClicked(index) {
        this.activeIndex = index;
        this.onEnter();
      },

    }));

  });
</script>