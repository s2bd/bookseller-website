/* JavaScript from Bookseller Template by Dewan Mukto, MuxAI 2025*/
const orderNowBtn = document.getElementById("orderNowBtn");
const orderStatusBtn = document.getElementById("orderStatusBtn");
const orderModal = document.getElementById("orderModal");
const statusModal = document.getElementById("statusModal");
const closeOrderModal = document.getElementById("closeOrderModal");
const closeStatusModal = document.getElementById("closeStatusModal");

document.addEventListener("DOMContentLoaded", function () {
    // Show the modal if there is a reference code in the URL
    const urlParams = new URLSearchParams(window.location.search);
    const refCode = urlParams.get('ref');
    
    if (refCode) {
        fetchOrderDetails(refCode);
    }
});

function fetchOrderDetails(refCode) {
    fetch(`order-details.php?ref=${refCode}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Fill in order details in the response modal
                document.getElementById('responseContent').innerHTML = `
                    <div id="orderSuccess">Order submitted! Your reference code: <b id="refCode">${refCode}</b> 
                    <i id="copyIcon" class="fas fa-copy" onclick="copyRef()"></i></div>
                    <h2>Order Details</h2>
                    <p><b>Book:</b> ${data.order.title} by ${data.order.author}</p>
                    <p><b>Ordered by:</b> ${data.order.customer_name}</p>
                    <p><b>Delivery Address:</b> ${data.order.address}</p>
                    <p><b>Status:</b> ${data.order.status}</p>
                `;
                responseModal.style.display = 'flex';
            } else {
                // Handle case where order was not found
                document.getElementById('responseContent').innerHTML = `
                    <p style="color:red;">Order not found. Please check your reference code.</p>
                `;
                responseModal.style.display = 'flex';
            }
        })
        .catch(error => {
            console.error("Error fetching order details:", error);
            document.getElementById('responseContent').innerHTML = `
                <p style="color:red;">There was an error retrieving the order details. Please try again later.</p>
            `;
            responseModal.style.display = 'flex';
        });
}


document.querySelectorAll("#orderNowBtn").forEach(btn => {
    btn.addEventListener("click", () => {
        orderModal.style.display = "flex";
    });
});
document.querySelectorAll("#orderStatusBtn").forEach(btn => {
    btn.addEventListener("click", () => {
        statusModal.style.display = "flex";
    });
});

function copyRef() {const refCode = document.getElementById("refCode").innerText;navigator.clipboard.writeText(refCode);document.getElementById("copyIcon").classList.remove('fa-copy');document.getElementById("copyIcon").classList.add('fa-check');}

document.body.addEventListener('mousemove', (event) => {document.body.style.setProperty('--mouse-x', `${event.clientX}px`);document.body.style.setProperty('--mouse-y', `${event.clientY}px`);});
document.addEventListener("DOMContentLoaded", async function () {
    const bookContainer = document.getElementById("floatingBooks");
    bookContainer.innerHTML = ""; // Clear previous books
    try {
        const response = await fetch("get-bookcovers.php"); // Endpoint that returns JSON
        const bookCovers = await response.json(); // Parse the JSON response
        const bookCount = bookCovers.length;
        const bookWidth = 120; // Adjust based on actual book image width
        const gap = 30; // Space between books
        const totalWidth = bookCount * (bookWidth + gap);
        const startX = (window.innerWidth - totalWidth) / 2 - 90;
        bookCovers.forEach((cover, index) => {
            const img = document.createElement("img");
            img.src = `asset/bookcovers/${cover}`; // Construct full path
            img.classList.add("floating-book");
            const xPos = startX + index * (bookWidth + gap);
            img.style.left = `${xPos}px`;
            img.style.bottom = "-125px";
            img.style.transform = "rotate(15deg)";
            img.addEventListener("mouseenter", () => {
                img.style.transform = "rotate(0deg) translateY(-80px)";
                img.style.transition = "transform 0.3s ease-in-out";
            });
            img.addEventListener("mouseleave", () => {
                img.style.transform = "rotate(15deg) translateY(0px)";
                img.style.transition = "transform 0.3s ease-in-out";
            });
            bookContainer.appendChild(img);
        });
    } catch (error) {
        console.error("Error loading book covers:", error);
    }
});

window.onclick = function(event) {
    if (event.target == orderModal || event.target == statusModal || event.target == document.getElementById("responseModal")) {
        event.target.style.display = "none";
    }
};