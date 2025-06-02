@php
    $account = $user->account;
@endphp
<x-front-logged-in-layout :event="$event">


    <x-front.session-notifs />

    <div class="container position-relative">

        <h3 class="mb-4 p-2 bg-primary-subtle rounded-1">Hébergement</h3>

        <div class="container-accommodation">

            @for($j = 1; $j <= 3; $j++)
                <div x-data="{
                    isOpen: false,
                }" class="card card-bordered mb-3">
                    <div class="card-body">
                        <div class="row justify-content-between">
                            <div class="col-lg-6">
                                <h3 class="mb-0">HOTEL DE NORMANDIE BORDEAUX</h3>
                                <p class="mb-0 fs-5">Sous - titre</p>
                                <div class="mt-3 fs-12">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    9 Cours du 30 Juillet 33000 Bordeaux (à 2,1 km de l'événement)
                                </div>

                                <ul class="list-inline mb-0">
                                    <li class="list-inline-item me-0 small">
                                        <i class="fas fa-star text-warning"></i></li>
                                    <li class="list-inline-item me-0 small">
                                        <i class="fas fa-star text-warning"></i></li>
                                    <li class="list-inline-item me-0 small">
                                        <i class="fas fa-star text-warning"></i></li>
                                    <li class="list-inline-item me-0 small">
                                        <i class="fas fa-star text-warning"></i></li>
                                    <li class="list-inline-item me-0 small">
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                    </li>
                                </ul>
                                <p class="mb-3 text-light-emphasis">A partir de <b>150€</b> / nuit
                                </p>


                                <p>
                                    Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                                    Accusamus aperiam asperiores aspernatur error expedita
                                    explicabo, illum laboriosam, laudantium maiores, optio porro
                                    provident quis suscipit tempore tenetur vero voluptatum.
                                    Commodi, illo.
                                </p>
                                <p>
                                    Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                                    Accusamus aperiam asperiores aspernatur error expedita
                                    explicabo, illum laboriosam, laudantium maiores, optio porro
                                    provident quis suscipit tempore tenetur vero voluptatum.
                                    Commodi, illo.
                                </p>

                            </div>
                            <div class="col-lg-6 position-relative">
                                <div class="tab-content mb-0 pb-0"
                                     id="course-pills-tabContent1"
                                     x-data="{ mainImage: '{{asset('front/bstheme/images/hotel/hotel-room.jpg')}}' }">
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
                                                    <a href="{{asset('front/bstheme/images/hotel/hotel-room-2.jpg')}}"
                                                       data-lightbox="gallery-{{$j}}">
                                                        <img src="{{asset('front/bstheme/images/hotel/hotel-room-2.jpg')}}" />
                                                    </a>
                                                    <a href="{{asset('front/bstheme/images/hotel/hotel-room-3.jpg')}}"
                                                       data-lightbox="gallery-{{$j}}">
                                                        <img src="{{asset('front/bstheme/images/hotel/hotel-room-3.jpg')}}" />
                                                    </a>
                                                    <a href="{{asset('front/bstheme/images/hotel/hotel-room-4.jpg')}}"
                                                       data-lightbox="gallery-{{$j}}">
                                                        <img src="{{asset('front/bstheme/images/hotel/hotel-room-4.jpg')}}" />
                                                    </a>

                                                </div>
                                            </div>

                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <!-- Clicking these images updates the mainImage property -->
                                                    <div class="col-4"
                                                         x-on:click="mainImage = '{{asset('front/bstheme/images/hotel/hotel-room-2.jpg')}}'">
                                                        <img src="{{asset('front/bstheme/images/hotel/hotel-room-2.jpg')}}"
                                                             alt="">
                                                    </div>
                                                    <div class="col-4"
                                                         x-on:click="mainImage = '{{asset('front/bstheme/images/hotel/hotel-room-3.jpg')}}'">
                                                        <img src="{{asset('front/bstheme/images/hotel/hotel-room-3.jpg')}}"
                                                             alt="">
                                                    </div>
                                                    <div class="col-4"
                                                         x-on:click="mainImage = '{{asset('front/bstheme/images/hotel/hotel-room-4.jpg')}}'">
                                                        <img src="{{asset('front/bstheme/images/hotel/hotel-room-4.jpg')}}"
                                                             alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4 mb-3 mb-sm-0 mt-lg-0">
                            <a x-show="!isOpen" href="#" @click.prevent="isOpen=!isOpen">
                                <i class="bi bi-chevron-down "></i> Afficher plus
                            </a>
                            <a x-show="isOpen" href="#" @click.prevent="isOpen=!isOpen">
                                <i class="bi bi-chevron-up "></i> Afficher moins
                            </a>
                        </div>
                        <div x-show="isOpen" x-transition class="row">
                            <div class="col">
                                <h5>Prestations de l'hôtel</h5>
                                <div class="small mb-2 d-flex align-items-center gap-2">
                                    <div>
                                        <i class="bi bi-wifi"></i>
                                        Wifi
                                    </div>
                                    <div>
                                        <i class="bi bi-cup"></i>
                                        Petit déjeuner
                                    </div>
                                    <div>
                                        <i class="bi bi-alarm"></i>
                                        Autre prestation de l'hôtel
                                    </div>
                                </div>
                                <div class="text-danger fs-6">
                                    <i class="bi bi-exclamation-circle"></i>&nbsp;Frais de dossier
                                    10€ par chambre réservée
                                </div>

                                <div class="table-responsive border-0 mt-3">
                                    <table class="table table-dark-gray align-middle table-accommodation p-4 mb-0">
                                        <thead>
                                        <tr>
                                            <th scope="col" class="border-0 rounded-start">Type de
                                                chambre
                                            </th>
                                            <th scope="col" class="border-0">Dates</th>
                                            <th scope="col" class="border-0">Nb de personnes</th>
                                            <th scope="col" class="border-0">Détails acommpagnants
                                            </th>
                                            <th scope="col" class="border-0">Prix</th>
                                            <th scope="col" class="border-0">Frais de dossier</th>
                                            <th scope="col" class="border-0">Commentaires</th>
                                            <th scope="col" class="border-0 rounded-end">
                                                Actions
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @for($i=1; $i<=5; $i++)
                                            <tr>
                                                <td>
                                                    <h6 class="table-responsive-title mt-2 mt-lg-0 mb-0">
                                                        Simple supérieur</h6>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        Du:
                                                        <div class="input-group">
                                                            <input type="text"
                                                                   name="birth"
                                                                   value="06/05/1978"
                                                                   class="form-control form-control-sm rounded-0"
                                                                   x-mask="99/99/9999"
                                                                   placeholder="jj/mm/aaaa"
                                                                   id="input_birth">
                                                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex gap-2 align-items-center mt-2">
                                                        Au:
                                                        <div class="input-group">
                                                            <input type="text"
                                                                   name="birth"
                                                                   value="06/05/1978"
                                                                   class="form-control form-control-sm rounded-0"
                                                                   x-mask="99/99/9999"
                                                                   placeholder="jj/mm/aaaa"
                                                                   id="input_birth">
                                                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                                        </div>
                                                    </div>

                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm"
                                                            name=""
                                                            id="">
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                    </select>
                                                </td>
                                                <td>
                                                <textarea placeholder="Nom, prénom et date de naissance de chaque accompagnant"
                                                          class="form-control form-control-sm"
                                                          rows="3"></textarea>
                                                </td>
                                                <td class="dropup text-nowrap">250€
                                                    <!-- Drop down with id -->
                                                    <a href="#"
                                                       class="h6 mb-0 text-danger"
                                                       role="button"
                                                       id="dropdownShare"
                                                       data-bs-toggle="dropdown"
                                                       aria-expanded="false">
                                                        <i class="bi bi-info-circle-fill"></i>
                                                    </a>
                                                    <ul class="dropdown-menu dropdown-w-sm dropdown-menu-end min-w-auto shadow rounded"
                                                        aria-labelledby="dropdownShare"
                                                        style="">
                                                        <li>
                                                        <span class="small">
                                                      Prise en charge grant industrie
                                                            </span>
                                                            <hr class="my-1">
                                                        </li>
                                                        <li>
                                                        <span class="small">
                                                      100€ le 09/09/22
                                                            </span>
                                                            <hr class="my-1">
                                                        </li>
                                                        <li>
                                                        <span class="small">
                                                      100€ le 10/09/22
                                                            </span>
                                                        </li>
                                                    </ul>
                                                </td>
                                                <td>10€</td>
                                                <td>
                                                <textarea placeholder="Commentaires"
                                                          class="form-control form-control-sm"
                                                          rows="3"></textarea>
                                                </td>
                                                <td>
                                                    <button href="#"
                                                            class="btn btn-sm btn-primary-soft mb-1 mb-sm-0">
                                                        Réserver
                                                    </button>
                                                </td>
                                            </tr>
                                        @endfor

                                        </tbody>
                                        <!-- Table body END -->
                                    </table>
                                    <!-- Table END -->
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>

    </div>

    <x-use-lightbox />

</x-front-logged-in-layout>
