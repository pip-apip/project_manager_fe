console.log("Hello, World!");
async function fetchData({ url, token }) {
    const access_token = token;
    const apiUrl = `${url}?limit=1`;
    try {
        const response = await fetch(apiUrl, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${access_token}`,
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const json = await response.json();

        if (json.status === 200) {
            // renderTable(json.data);
            // renderPagination(json.pagination);
            console.log(json);
        } else {
            console.error('Unexpected response status:', json.message);
        }
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}