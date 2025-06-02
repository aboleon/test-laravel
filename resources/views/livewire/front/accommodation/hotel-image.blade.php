<div class="col-lg-6 position-relative">
    <div class="tab-content mb-0 pb-0"
         id="course-pills-tabContent1"
         x-data="{ mainImage: '{{$firstPhoto}}' }">
        <div class="tab-pane fade show active"
             id="course-pills-tab01"
             role="tabpanel"
             aria-labelledby="course-pills-tab-01">
            <div class="card p-2 pb-0 shadow">
                <div class="overflow-hidden h-xl-200px">
                    <a :href="mainImage"
                       data-lightbox="gallery-{{$j}}">
                        <img :src="mainImage"
                             class="card-img-top"
                             alt="course image"
                        >
                    </a>

                    <div class="d-none">
                        @foreach($hotel->media as $media)
                            @if($media->url() !== $firstPhoto)
                                <a href="{{$media->url()}}"
                                   data-lightbox="gallery-{{$j}}">
                                    <img alt="hotel" src="{{$media->url()}}"/>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        @php
                            $n = 0;
                        @endphp
                        @foreach ($hotel->media as $media)
                            <div class="col text-center"
                                 x-on:click="mainImage = '{{$media->url()}}'">
                                <img src="{{$media->url()}}"
                                     class="h-70px object-fit-cover"
                                     alt="photo hotel">
                            </div>
                            @php
                                $n++;
                            @endphp
                        @endforeach

                        @if($n < 3)
                            @for($i = 0; $i < 3 - $n; $i++)
                                <div class="col text-center"></div>
                            @endfor
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
