$(document).ready(function () {
	$("#slotModal").on("show.bs.modal", function (event) {
		let button = $(event.relatedTarget)
		let slotNumber = button.data("slot-number")
		let status = button.data("status")
		let vehicleId = button.data("vehicle-id")
		let userId = button.data("user-id")
		let modal = $(this)
		modal.find("#slotNumber").text(slotNumber)
		modal.find("#hiddenSlotNumber").val(slotNumber)
		modal.find('input[name="status"][value="' + status + '"]').prop("checked", true)
		modal.find("#userId").val(userId)

		// Disable user select if status is available
		if (status === "available") {
			modal.find("#userId").prop("disabled", true)
		} else {
			modal.find("#userId").prop("disabled", false)
		}
	})

	$("#infoModal").on("show.bs.modal", function (event) {
		let button = $(event.relatedTarget)
		let modal = $(this)
		modal.find("#infoFname").text(button.data("fname"))
		modal.find("#infoLname").text(button.data("lname"))
		modal.find("#infoEmail").text(button.data("email"))
		modal.find("#infoPhone").text(button.data("contact-no"))
		modal.find("#makeAvailableButton").data("slot-number", button.data("slot-number"))
	})

	$("#makeAvailableButton").on("click", function () {
		let slotNumber = $(this).data("slot-number")
		$.ajax({
			url: "adminmap.php",
			method: "POST",
			data: {
				slotNumber: slotNumber,
				status: "available",
				user_id: null,
			},
			success: function (response) {
				// Handle success response
				$("#infoModal").modal("hide")
				location.reload() // Reload the page to reflect changes
			},
		})
	})

	$('input[name="status"]').on("change", function () {
		let status = $(this).val()
		if (status === "available") {
			$("#userId").prop("disabled", true).val("")
		} else {
			$("#userId").prop("disabled", false)
		}
	})

	$("#userId").on("change", function () {
		let selectedOption = $(this).find("option:selected")
		let vehicleId = selectedOption.data("vehicle-id")
		$("#vehicleId").val(vehicleId)
	})

	$("#saveChanges").on("click", function () {
		let formData = $("#slotForm").serialize()
		$.ajax({
			url: "adminmap.php",
			method: "POST",
			data: formData,
			success: function (response) {
				// Handle success response
				$("#slotModal").modal("hide")
				location.reload() // Reload the page to reflect changes
			},
		})
	})
})
