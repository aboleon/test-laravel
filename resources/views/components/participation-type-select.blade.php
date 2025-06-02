<x-mfw::select
        :name="$name"
        label="Type de participation"
        :group="true"
        :values="$participationTypes()"
        :affected="$affected" />
