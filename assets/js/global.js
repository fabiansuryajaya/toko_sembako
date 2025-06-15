/**
 * Kirim request POST ke API
 * @param {string} url - URL endpoint (misal: 'api/auth.php')
 * @param {FormData} formData - Data form (FormData)
 * @returns {Promise<object>} - Response JSON
 */
async function callAPI({url, body, method = 'POST'}) {
  try {
    const response = await fetch(url, {
      method,
      body : JSON.stringify(body),
      headers: {
        'Content-Type': 'application/json'
      }
    });

    if (!response.ok) {
      throw new Error('Network response was not ok');
    }

    return await response.json();
  } catch (error) {
    console.error('API Error:', error);
    return { status: '500', message: 'Terjadi kesalahan, silakan coba lagi' };
  }
}
