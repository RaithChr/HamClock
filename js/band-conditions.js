/**
 * Band Conditions - N0NBH Integration
 * Fetches real band conditions from N0NBH (hamqsl.com) via server proxy
 * Includes day/night conditions and VHF propagation
 */

const bandRangeMap = {
    '80m-40m': ['80m', '60m', '40m'],
    '30m-20m': ['30m', '20m'],
    '17m-15m': ['17m', '15m'],
    '12m-10m': ['12m', '11m', '10m']
};

const bandConditionMap = {
    'GOOD': { class: 'good', text: 'GOOD', color: '#00ff88' },
    'FAIR': { class: 'fair', text: 'FAIR', color: '#ffa500' },
    'POOR': { class: 'poor', text: 'POOR', color: '#ff6482' }
};

async function fetchN0NBHData() {
    try {
        const response = await fetch('/fetch-n0nbh.php');
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        return await response.json();
    } catch (error) {
        console.warn('N0NBH fetch failed, using fallback:', error);
        return null;
    }
}

function normalizeCondition(cond) {
    if (!cond) return 'FAIR';
    const upper = String(cond).toUpperCase().trim();
    if (upper === 'GOOD') return 'GOOD';
    if (upper === 'POOR') return 'POOR';
    return 'FAIR';
}

async function updateAllBandConditions() {
    try {
        const n0nbh = await fetchN0NBHData();
        if (!n0nbh) {
            console.warn('N0NBH data unavailable');
            return;
        }
        
        // Build condition map from N0NBH
        const conditionMap = { day: {}, night: {} };
        if (n0nbh.bandConditions) {
            for (const bc of n0nbh.bandConditions) {
                const time = bc.time || 'day';
                const name = bc.name || '';
                conditionMap[time] = conditionMap[time] || {};
                conditionMap[time][name] = normalizeCondition(bc.condition);
            }
        }
        
        // Determine current time (UTC-based, simplified)
        const hour = new Date().getUTCHours();
        const isDaytime = hour >= 6 && hour <= 18;
        const currentTime = isDaytime ? 'day' : 'night';
        
        // Expand ranges to individual bands
        const bands = [];
        for (const [range, individualBands] of Object.entries(bandRangeMap)) {
            const dayCondition = conditionMap.day?.[range] || 'FAIR';
            const nightCondition = conditionMap.night?.[range] || 'FAIR';
            const currentCondition = currentTime === 'day' ? dayCondition : nightCondition;
            
            for (const band of individualBands) {
                bands.push({
                    band,
                    condition: currentCondition,
                    day: dayCondition,
                    night: nightCondition
                });
            }
        }
        
        // Update DOM
        bands.forEach((b) => {
            const boxId = `band${b.band}-box`;
            const box = document.getElementById(boxId);
            if (!box) return;
            
            const condData = bandConditionMap[b.condition] || bandConditionMap['FAIR'];
            box.className = `band-box ${condData.class}`;
            
            const condElement = box.querySelector('.band-condition');
            if (condElement) {
                condElement.textContent = condData.text;
            }
        });
        
        console.log('✅ Band Conditions updated from N0NBH');
        
    } catch (error) {
        console.error('Band conditions update error:', error);
    }
}

window.updateAllBandConditions = updateAllBandConditions;
