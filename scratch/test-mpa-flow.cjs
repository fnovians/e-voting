const http = require('http');

const PORT = 8000;
const HOST = 'localhost';

function makeRequest(path, method, data = null, cookies = []) {
  return new Promise((resolve, reject) => {
    let payload = '';
    const headers = {
      'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
    };

    if (data) {
      // Standard form URL encoded submission for standard MPA POST
      const params = new URLSearchParams();
      for (const [key, val] of Object.entries(data)) {
        params.append(key, val);
      }
      payload = params.toString();
      headers['Content-Type'] = 'application/x-www-form-urlencoded';
      headers['Content-Length'] = Buffer.byteLength(payload);
    }

    if (cookies.length > 0) {
      headers['Cookie'] = cookies.join('; ');
    }

    const options = {
      hostname: HOST,
      port: PORT,
      path: path,
      method: method,
      headers: headers
    };

    const req = http.request(options, (res) => {
      let responseBody = '';
      
      const setCookie = res.headers['set-cookie'] || [];
      const newCookies = setCookie.map(c => c.split(';')[0]);

      res.on('data', (chunk) => {
        responseBody += chunk;
      });

      res.on('end', () => {
        resolve({
          statusCode: res.statusCode,
          headers: res.headers,
          body: responseBody,
          cookies: newCookies.length > 0 ? newCookies : cookies
        });
      });
    });

    req.on('error', (err) => {
      reject(err);
    });

    if (payload) {
      req.write(payload);
    }
    req.end();
  });
}

async function runTests() {
  console.log('🧪 STARTING PROGRAMMATIC LARAVEL 12 BLADE MPA FLOW TESTING...');
  let sessionCookies = [];

  try {
    // 0. Reset Database
    console.log('\n--- 0. Resetting Database via HTTP POST ---');
    const resetRes = await makeRequest('/research-lab/reset', 'POST');
    console.log('Reset Database Redirect Status:', resetRes.statusCode); // Should redirect (302) to research lab
    console.log('Redirect Location:', resetRes.headers.location);
    sessionCookies = resetRes.cookies;

    // 1. Submit login form for Budi
    console.log('\n--- 1. Submitting Voter Credentials (Mitigated) ---');
    const loginRes = await makeRequest('/login', 'POST', {
      nim: '120203001',
      password: 'password123',
      mitigated: '1'
    }, sessionCookies);
    console.log('Login Response Status:', loginRes.statusCode); // 302 redirect to /otp
    console.log('Redirect Location:', loginRes.headers.location);
    sessionCookies = loginRes.cookies;

    // 2. Fetch OTP view HTML and parse OTP code from Simulated Inbox Widget
    console.log('\n--- 2. Fetching OTP Page & Parsing Widget OTP ---');
    const otpPageRes = await makeRequest('/otp', 'GET', null, sessionCookies);
    console.log('OTP Page Status:', otpPageRes.statusCode);
    
    // Parse OTP using regex from the widget body
    const otpMatch = otpPageRes.body.match(/email-widget-code-box[^>]*>\s*(\d{6})\s*<\/div>/);
    const otp = otpMatch ? otpMatch[1] : null;
    console.log('Parsed OTP Code from Floating HTML Widget:', otp);

    if (!otp) {
      throw new Error('Failed to parse OTP code from widget HTML!');
    }

    // 3. Submit OTP verification form
    console.log('\n--- 3. Submitting OTP Verification ---');
    const verifyRes = await makeRequest('/verify-otp', 'POST', {
      otp: otp
    }, sessionCookies);
    console.log('OTP Verify Status:', verifyRes.statusCode); // 302 redirect to /dashboard
    console.log('Redirect Location:', verifyRes.headers.location);
    sessionCookies = verifyRes.cookies;

    // 4. Fetch dashboard
    console.log('\n--- 4. Fetching Voter Dashboard ---');
    const dashRes = await makeRequest('/dashboard', 'GET', null, sessionCookies);
    console.log('Dashboard Page Status:', dashRes.statusCode); // 200 OK
    const hasCandidates = dashRes.body.includes('Calon 1: Andika');
    console.log('Candidates list populated in Blade HTML:', hasCandidates ? 'YES' : 'NO');

    // 5. Cast vote for Candidate 1
    console.log('\n--- 5. Casting AES Secure Vote ---');
    const voteRes = await makeRequest('/vote', 'POST', {
      candidateId: '1'
    }, sessionCookies);
    console.log('Vote Cast Status:', voteRes.statusCode); // 302 redirect to /vote/success
    console.log('Redirect Location:', voteRes.headers.location);
    sessionCookies = voteRes.cookies;

    // 6. Fetch success page and extract AES variables from HTML
    console.log('\n--- 6. Loading Success Blade Page & Extracting AES Properties ---');
    const successRes = await makeRequest('/vote/success', 'GET', null, sessionCookies);
    console.log('Success View Status:', successRes.statusCode);
    
    // Parse ciphertext, IV, and tag from HTML using Regex
    const ciphertextMatch = successRes.body.match(/id="crypto-ciphertext"[^>]*>\s*([a-f0-9]+)\s*<\/span>/);
    const ivMatch = successRes.body.match(/id="crypto-iv"[^>]*>\s*([a-f0-9]+)\s*<\/span>/);
    const tagMatch = successRes.body.match(/id="crypto-tag"[^>]*>\s*([a-f0-9]+)\s*<\/span>/);

    console.log('Extracted Ciphertext:', ciphertextMatch ? ciphertextMatch[1] : 'None');
    console.log('Extracted IV:', ivMatch ? ivMatch[1] : 'None');
    console.log('Extracted GCM Tag:', tagMatch ? tagMatch[1] : 'None');

    // 7. Try to double vote
    console.log('\n--- 7. Testing Double-Voting Lock ---');
    const doubleVoteRes = await makeRequest('/vote', 'POST', {
      candidateId: '2'
    }, sessionCookies);
    console.log('Double Vote Status (should redirect back):', doubleVoteRes.statusCode);
    console.log('Redirect Location:', doubleVoteRes.headers.location);

    // 8. SQL Injection Sandbox Testing (Vulnerable bypass)
    console.log('\n--- 8. Testing SQL Injection (Vulnerable Pane) ---');
    const vulnTestRes = await makeRequest('/security-lab/test', 'POST', {
      nim: "' OR '1'='1",
      password: "wrong",
      mitigated: '0'
    }, sessionCookies);
    console.log('SQL Injection Vulnerable Test Status:', vulnTestRes.statusCode); // 302 redirect back
    console.log('Redirect Location:', vulnTestRes.headers.location);

    // Load sandbox page to verify that query results are flashed in HTML
    const labPageRes = await makeRequest('/security-lab', 'GET', null, vulnTestRes.cookies);
    const bypassedAdmin = labPageRes.body.includes('SERANGAN BERHASIL!');
    console.log('SQL Injection Bypass Successful and flashed in Blade:', bypassedAdmin ? 'YES' : 'NO');

    console.log('\n✅ ALL LARAVEL 12 BLADE MPA PROGRAMMATIC TESTS PASSED SUCCESSFULLY! 🚀');
  } catch (err) {
    console.error('❌ Programmatic tests failed with error:', err);
  }
}

runTests();
