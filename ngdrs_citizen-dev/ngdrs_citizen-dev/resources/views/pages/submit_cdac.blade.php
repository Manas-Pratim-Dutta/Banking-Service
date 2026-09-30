<form
id="cdacForm"
method="POST"
action="{{ config('esign.url') }}"
>

<input
type="hidden"
name="eSignRequest"
value="{{ htmlspecialchars($signXML) }}"
>

</form>


<script>

document
.getElementById('cdacForm')
.submit();

</script>