<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Roboto+Mono:400,700|Roboto:400,700');

        .md-default {
            font-family: Roboto, Arial, "sans serif";
            line-height: 1.5;
            font-size: .8rem;
            text-rendering: optimizeLegibility;
            color: #50596c;
        }

        .md-default *,
        .md-default *:before,
        .md-default *:after {
            box-sizing: border-box;
        }

        .md-default .larger {
            font-size: 1.2em;
        }

        .md-default .smaller {
            font-size: .8em;
        }

        .md-default.presentation,
        .md-default .presentation-display {
            font-size: 1.2rem;
        }

        .md-default h1 {
            font-size: 2.00em;
        }

        .md-default h2 {
            font-size: 1.50em;
        }

        .md-default h3 {
            font-size: 1.17em;
        }

        .md-default h4 {
            font-size: 1.00em;
        }

        .md-default h5 {
            font-size: 0.83em;
        }

        .md-default h6 {
            font-size: 0.67em;
        }

        .md-default a {
            color: inherit;
            text-decoration: none;
            border-bottom: 1px dashed;
        }

        .md-default a:hover {
            color: inherit;
            background-color: #eee;
            text-decoration: none;
        }

        .md-default thead tr {
            background-color: #607D8B;
            color: #ECEFF1;
        }

        .md-default tbody tr:nth-child(odd) {
            background-color: #ECEFF1;
        }

        .md-default tbody tr:nth-child(even) {
            background-color: #CFD8DC;
        }

        .md-default tbody tr:hover {
            box-shadow: 0px 0px 2px 2px #607D8B;
            z-index: 10;
        }

        .md-default td,
        .md-default th {
            padding: 5px;
        }

        .md-default code,
        .md-default kbd,
        .md-default pre,
        .md-default samp {
            font-family: "Roboto Mono", "SF Mono", "Segoe UI Mono", Menlo, Courier, monospace;
        }

        .md-default code {
            display: initial;
            border-radius: .15rem;
            font-size: 85%;
            line-height: 1.2;
            padding: .1rem .15rem;
            border: 1px solid #ddd;
        }

        .md-default pre > code {
            display: block;
            margin: 10px;
            overflow: auto;
        }

        .md-default kbd {
            background: #454d5d;
            border-radius: .1rem;
            color: #fff;
            font-size: .7rem;
            line-height: 1.2;
            padding: .1rem .15rem;
        }

        .md-default blockquote {
            border-left: .1rem solid #e7e9ed;
            margin-left: 0;
            padding: .4rem .8rem;
        }

        .md-default .task-list-item input {
            margin: 0 0.35em 0 -.5em !important;
        }

        .md-default ul,
        .md-default ol {
            margin: .8rem 0 .8rem 1.5rem;
        }

        .md-default ul {
            list-style: disc outside;
        }

        .md-default ul ul {
            list-style-type: circle;
        }

        .md-default ol {
            list-style: decimal outside;
        }

        .md-default ol ol {
            list-style-type: lower-alpha;
        }

        .md-default p {
            margin: 0;
            margin-bottom: .5em;
        }

        .md-default li {
            margin-top: 0;
        }

        .md-default .timeline {
            position: relative;
            display: flex;
            flex-direction: row;
            margin: 0;
            border: 0;
            padding: 0;
        }

        .md-default .timeline p {
            margin: 0;
        }

        /*
        .md-default .timeline .entry {
          flex-grow: 1;
          display: flex;
          flex-direction: row;
        } */

        .md-default .timeline .title {
            flex-shrink: 0;
            flex-grow: 1;
            flex-basis: 0;
            border-right: 2px solid #ddd;
            text-align: right;
            padding-right: 2rem;
            position: relative;
        }

        .md-default .timeline .body {
            text-align: left;
            padding-left: 2rem;
            flex-shrink: 0;
            flex-grow: 3;
            flex-basis: 0;
            height: auto;
        }

        .md-default .timeline .title,
        .md-default .timeline .body {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }

        .md-default .timeline + .timeline .title,
        .md-default .timeline + .timeline .body {
            padding-top: 0rem;
        }

        .md-default .timeline .title .head {
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .md-default .timeline .title .head::after {
            content: "";
            position: absolute;
            right: -7px;
            border: 3px solid salmon;
            background-color: salmon;
            width: 12px;
            height: 12px;
            border-radius: 100%;
        }

        .md-default .timeline:hover .title .head::after {
            background-color: white;
        }

        .md-default .timeline .content {
            color: #999999;
        }

        .md-default .timeline .body .content {
            padding-left: 1em;
        }

        /* .md-default .timeline .title > input[type="checkbox"],
        .md-default .timeline .body > input[type="checkbox"] {
          display: none;
        }

        .md-default .timeline .title > input[type="checkbox"] + .content,
        .md-default .timeline .body > input[type="checkbox"] + .content {
          display: none;
        }

        .md-default .timeline .title > input[type="checkbox"]:checked + .content,
        .md-default .timeline .body > input[type="checkbox"]:checked + .content {
          display: block;
        } */

        /*

        Original highlight.js style (c) Ivan Sagalaev <maniac@softwaremaniacs.org>

        */

        .hljs {
            display: block;
            overflow-x: auto;
            padding: 0.5em;
            background: #F0F0F0;
        }


        /* Base color: saturation 0; */

        .hljs,
        .hljs-subst {
            color: #444;
        }

        .hljs-comment {
            color: #888888;
        }

        .hljs-keyword,
        .hljs-attribute,
        .hljs-selector-tag,
        .hljs-meta-keyword,
        .hljs-doctag,
        .hljs-name {
            font-weight: bold;
        }


        /* User color: hue: 0 */

        .hljs-type,
        .hljs-string,
        .hljs-number,
        .hljs-selector-id,
        .hljs-selector-class,
        .hljs-quote,
        .hljs-template-tag,
        .hljs-deletion {
            color: #880000;
        }

        .hljs-title,
        .hljs-section {
            color: #880000;
            font-weight: bold;
        }

        .hljs-regexp,
        .hljs-symbol,
        .hljs-variable,
        .hljs-template-variable,
        .hljs-link,
        .hljs-selector-attr,
        .hljs-selector-pseudo {
            color: #BC6060;
        }


        /* Language color: hue: 90; */

        .hljs-literal {
            color: #78A960;
        }

        .hljs-built_in,
        .hljs-bullet,
        .hljs-code,
        .hljs-addition {
            color: #397300;
        }


        /* Meta color: hue: 200 */

        .hljs-meta {
            color: #1f7199;
        }

        .hljs-meta-string {
            color: #4d99bf;
        }


        /* Misc effects */

        .hljs-emphasis {
            font-style: italic;
        }

        .hljs-strong {
            font-weight: bold;
        }

    </style>
</head>
<body class='md-default'>
<h1>Documentation de l'API</h1>
<p>Bienvenue dans la documentation de notre API.
    Cette API vous permet d'accéder à diverses informations liées à notre plateforme,
    notamment les détails des hôtels, des sessions et des interventions de programme,
    ainsi que des informations sur les intervenants.
    D'autres informations pourraient être ajoutées à l'avenir.
    Voici comment vous pouvez utiliser nos différents points de terminaison :</p>
<h2>Points de terminaison de l'API</h2>
<h3>1. Informations sur les Hôtels</h3>
<ul>
    <li><p><strong>Obtenir les détails d'un hôtel spécifique</strong><br />
            <strong>URI :</strong>
            <code class="hljs">/api/json?action=hotel&amp;id={id_hotel}</code><br />
            <strong>Méthode :</strong> GET<br />
            <strong>Description :</strong> Renvoie les détails d'un hôtel spécifique.<br />
            <strong>Paramètres :</strong></p>
        <ul>
            <li><code class="hljs">id_hotel</code> : Identifiant unique de l'hôtel.</li>
        </ul>
    </li>
    <li><p><strong>Obtenir les détails d'un hôtel pour un événement spécifique</strong><br />
            <strong>URI :</strong> <code class="hljs">/api/json?action=hotelByEvent&amp;event_id={id_evenement}&amp;hotel_id={id_hotel}</code><br />
            <strong>Méthode :</strong> GET<br />
            <strong>Description :</strong> Renvoie les détails d'un hôtel associé à un événement
            spécifique.<br />
            <strong>Paramètres :</strong></p>
        <ul>
            <li><code class="hljs">id_evenement</code> : Identifiant unique de l'événement.</li>
            <li><code class="hljs">id_hotel</code> : Identifiant unique de l'hôtel.</li>
        </ul>
    </li>
</ul>
<h3>2. Informations sur les Interventions de Programme</h3>
<ul>
    <li><p><strong>Obtenir les interventions par session</strong><br />
            <strong>URI :</strong> <code class="hljs">/api/json?action=interventionsBySession&amp;id={id_session}</code><br />
            <strong>Méthode :</strong> GET<br />
            <strong>Description :</strong> Renvoie les interventions associées à une session
            spécifique.<br />
            <strong>Paramètres :</strong></p>
        <ul>
            <li><code class="hljs">id_session</code> : Identifiant unique de la session.</li>
        </ul>
    </li>
    <li><p><strong>Obtenir les interventions par conteneur</strong><br />
            <strong>URI :</strong> <code class="hljs">/api/json?action=interventionsByContainer&amp;id={id_conteneur}</code><br />
            <strong>Méthode :</strong> GET<br />
            <strong>Description :</strong> Renvoie les interventions associées à un conteneur
            spécifique.<br />
            <strong>Paramètres :</strong></p>
        <ul>
            <li><code class="hljs">id_conteneur</code> : Identifiant unique du conteneur.</li>
        </ul>
    </li>
    <li><p><strong>Obtenir les interventions par événement</strong><br />
            <strong>URI :</strong> <code class="hljs">/api/json?action=interventionsByEvent&amp;id={id_evenement}</code><br />
            <strong>Méthode :</strong> GET<br />
            <strong>Description :</strong> Renvoie les interventions associées à un événement
            spécifique.<br />
            <strong>Paramètres :</strong></p>
        <ul>
            <li><code class="hljs">id_evenement</code> : Identifiant unique de l'événement.</li>
        </ul>
    </li>
</ul>
<h3>3. Informations sur les Intervenants</h3>
<ul>
    <li><strong>Obtenir les intervenants par intervention</strong><br />
        <strong>URI :</strong> <code class="hljs">/api/json?action=oratorsByIntervention&amp;id={id_intervention}</code><br />
        <strong>Méthode :</strong> GET<br />
        <strong>Description :</strong> Renvoie les informations sur les intervenants participant à une
        intervention spécifique.<br />
        <strong>Paramètres :</strong>
        <ul>
            <li><code class="hljs">id_intervention</code> : Identifiant unique de l'intervention.
            </li>
        </ul>
    </li>
</ul>
<h4>Intervenants par Session</h4>
<ul>
    <li><strong>Obtenir les intervenants par session</strong><br />
        <strong>URI :</strong> <code class="hljs">/api/json?action=oratorsBySession&amp;id={id_session}</code><br />
        <strong>Méthode :</strong> GET<br />
        <strong>Description :</strong> Renvoie les informations sur les intervenants participant à une
        session spécifique.<br />
        <strong>Paramètres :</strong>
        <ul>
            <li><code class="hljs">id_session</code> : Identifiant unique de la session.</li>
        </ul>
    </li>
</ul>
<h4>Intervenants par Conteneur (couple salle/jour)</h4>
<ul>
    <li><strong>Obtenir les intervenants par conteneur</strong><br />
        <strong>URI :</strong> <code class="hljs">/api/json?action=oratorsByContainer&amp;id={id_dayRoom}</code><br />
        <strong>Méthode :</strong> GET<br />
        <strong>Description :</strong> Renvoie les informations sur les intervenants participant à un
        conteneur spécifique (couple salle/jour).<br />
        <strong>Paramètres :</strong>
        <ul>
            <li><code class="hljs">id_dayRoom</code> : Identifiant unique du conteneur (Salle de
                Jour).
            </li>
        </ul>
    </li>
</ul>
<h4>Intervenants par Événement</h4>
<ul>
    <li><strong>Obtenir les intervenants par événement</strong><br />
        <strong>URI :</strong> <code class="hljs">/api/json?action=oratorsByEvent&amp;id={id_evenement}</code><br />
        <strong>Méthode :</strong> GET<br />
        <strong>Description :</strong> Renvoie les informations sur les intervenants participant à un
        événement spécifique.<br />
        <strong>Paramètres :</strong>
        <ul>
            <li><code class="hljs">id_evenement</code> : Identifiant unique de l'événement.</li>
        </ul>
    </li>
</ul>
<h2>Utilisation</h2>
<p>Pour utiliser l'API, envoyez une requête HTTP GET à l'URI correspondant au point de terminaison
    souhaité,
    avec les paramètres appropriés.
    Les réponses seront renvoyées au format JSON.</p>
<p>Pour plus de détails sur les informations renvoyées, veuillez nous contacter directement. </p>
<h2>Gestion des erreurs</h2>
<p>Si un problème survient lors de l'utilisation de l'API, une réponse JSON contenant un message
    d'erreur sera renvoyée. Les erreurs courantes incluent l'accès à un identifiant inexistant ou la
    demande d'une action non trouvée.</p>
</body>
</html>
