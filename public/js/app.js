document.addEventListener('DOMContentLoaded', async () => {
    let positionsMap = new Map();
    let nextUrl = '/users';

    async function fetchPositions() {
        try {
            const response = await fetch('/positions');
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const positions = await response.json();
            const select = document.getElementById('position_id');
            positions.positions.forEach(position => {
                const option = document.createElement('option');
                option.value = position.id;
                option.textContent = position.name;
                select.appendChild(option);
                positionsMap.set(position.id, position.name);
            });
        } catch (error) {
            console.error('There was a problem with the fetch operation:', error);
        }
    }

    async function fetchUsers(url) {
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const users = await response.json();
            const tableBody = document.getElementById('users_table_body');
            users.users.forEach(user => {
                const row = document.createElement('tr');
                const idCell = document.createElement('td');
                idCell.textContent = user.id;
                const nameCell = document.createElement('td');
                nameCell.textContent = user.name;
                const emailCell = document.createElement('td');
                emailCell.textContent = user.email;
                const phoneCell = document.createElement('td');
                phoneCell.textContent = user.phone;
                const positionCell = document.createElement('td');
                positionCell.textContent = positionsMap.get(user.position_id) || 'Unknown';
                const photoCell = document.createElement('td');
                const photoImg = document.createElement('img');
                photoImg.src = '/storage/'+user.photo;
                photoImg.alt = user.name;
                photoImg.width = 70;
                photoImg.height = 70;
                photoCell.appendChild(photoImg);
                row.appendChild(idCell);
                row.appendChild(nameCell);
                row.appendChild(emailCell);
                row.appendChild(phoneCell);
                row.appendChild(positionCell);
                row.appendChild(photoCell);
                tableBody.appendChild(row);
            });
            nextUrl = users.links.next_url;
            document.getElementById('load_more').style.display = nextUrl ? 'block' : 'none';
        } catch (error) {
            console.error('There was a problem with the fetch operation:', error);
        }
    }

    async function handleFormSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        const errorBlock = document.querySelector('.errors');
        errorBlock.innerHTML = '';

        try {
            const tokenResponse = await fetch('/token');
            if (!tokenResponse.ok) {
                throw new Error('Network response was not ok');
            }
            const tokenData = await tokenResponse.json();
            const token = tokenData.token;

            const userResponse = await fetch('/users', {
                method: 'POST',
                headers: {
                    'Token': `${token}`
                },
                body: formData
            });
            if (!userResponse.ok) {
                const errorData = await userResponse.json();
                if (errorData.fails) {
                    errorBlock.textContent = errorData.message+"\n"+JSON.stringify(errorData.fails, null, 2);
                }
            } else {
                const userData = await userResponse.json();
                errorBlock.textContent = 'Success! new user id'+userData.user_id;
                
            }
        } catch (error) {
            console.error('There was a problem with the fetch operation:', error);
            alert('Failed to create user.');
        }
    }

    document.querySelector('.form').addEventListener('submit', handleFormSubmit);

    document.getElementById('load_more').addEventListener('click', () => {
        if (nextUrl) {
            fetchUsers(nextUrl);
        }
    });

    await fetchPositions();
    await fetchUsers(nextUrl);
});