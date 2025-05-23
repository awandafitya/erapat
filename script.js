document.addEventListener("DOMContentLoaded", function() {
    const calendarEl = document.getElementById("calendar");

    fetch("events.php")
        .then(response => response.json())
        .then(events => {
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: "dayGridMonth",
                initialDate: new Date(),
                selectable: true,
                events: events,
                dateClick: function(info) {
                    showAgenda(info.dateStr, events);
                }
            });
            calendar.render();
        });

    function showAgenda(date, events) {
        const modal = document.getElementById("agenda-modal");
        const agendaDetails = document.getElementById("agenda-details");

        const agendaForDate = events.filter(event => event.start === date);
        
        if (agendaForDate.length > 0) {
            agendaDetails.innerHTML = agendaForDate.map(event => 
                `<p><strong>${event.title}</strong>: ${event.description}</p>`
            ).join("");
        } else {
            agendaDetails.innerHTML = "<p>Tidak ada agenda pada tanggal ini.</p>";
        }

        modal.style.display = "block";
    }

    document.querySelector(".close").addEventListener("click", function() {
        document.getElementById("agenda-modal").style.display = "none";
    });

    window.onclick = function(event) {
        const modal = document.getElementById("agenda-modal");
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };
});


//profile 
document.addEventListener("DOMContentLoaded", function () {
    fetch("get_profile.php")
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert("Data tidak ditemukan! Silakan login kembali.");
                window.location.href = 'login.php';
            } else {
                document.getElementById('profile-name').textContent = data.nama;
                document.getElementById('nip').value = data.nip;
                document.getElementById('name').value = data.nama;
                document.getElementById('position').value = data.jabatan;
                document.getElementById('username').value = data.username;

                // Pastikan foto tampil jika ada, jika tidak pakai default
                let photo = data.foto ? data.foto : "images/default.png";
                document.getElementById('profile-photo').src = photo;
            }
        })
        .catch(error => console.error('Error fetching data:', error));
});
