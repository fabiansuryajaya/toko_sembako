/**
 * Kirim request POST ke API
 * @param {string} url - URL endpoint (misal: 'api/auth.php')
 * @param {FormData} formData - Data form (FormData)
 * @returns {Promise<object>} - Response JSON
 */
async function callAPI(url, formData) {
  try {
    const response = await fetch(url, {
      method: 'POST',
      body: formData
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
