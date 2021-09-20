function sendPage(mailHeadline)
{
	window.location="mailto:\?subject\=" + mailHeadline + "&body=" + mailHeadline + ": " + window.location;
}