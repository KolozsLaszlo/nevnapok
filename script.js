 document.getElementById('keresoForm').addEventListener('submit', async function(e) {
      e.preventDefault();

      const datum = document.getElementById('datum').value.trim();
      const nev = document.getElementById('nev').value.trim();
      const eredmenyDiv = document.getElementById('eredmeny');

      let url = 'http://localhost/api/nevnapok/';
      if (datum) {
        url += '?nap=' + encodeURIComponent(datum);
      } else if (nev) {
        url += '?nev=' + encodeURIComponent(nev);
      } else {
        eredmenyDiv.innerHTML = '<strong>Adj meg egy dátumot vagy nevet!</strong>';
        return;
      }

      eredmenyDiv.innerHTML = '<em>Keresés folyamatban...</em>';

      try {
        const response = await fetch(url);
        const data = await response.json();

        if (data.hiba) {
          eredmenyDiv.innerHTML = `<strong>Hiba: ${data.hiba}</strong>`;
        } else if (data.length === 0) {
          eredmenyDiv.innerHTML = '<strong>Nincs találat.</strong>';
        } else {
          let html = '<h2>Eredmény(ek):</h2><ul>';
          for (let sor of data) {
            html += `<li><strong>${sor.ho}.${sor.nap}</strong> — ${sor.nev1}${sor.nev2 ? ' és ' + sor.nev2 : ''}</li>`;
          }
          html += '</ul>';
          eredmenyDiv.innerHTML = html;
        }
      } catch (err) {
        eredmenyDiv.innerHTML = '<strong>Hálózati hiba történt.</strong>';
        console.error(err);
      }
    });