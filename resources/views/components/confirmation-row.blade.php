<tr>
    <td>{{ $services->where('id', $stockable->service_id)->first()?->title ?? 'NC' }}</td>
    <td>{{ $stockable->quantity }}</td>
    <td>{{ $unitPrice }}</td>
    <td>{{ $vat }}</td>
    <td>{{ $ttc }}</td>
</tr>
