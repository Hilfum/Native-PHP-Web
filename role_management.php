<?php
function handleLoketDocument($conn, $doc_id, $action) {
    switch($action) {
        case 'input':
            // When loket inputs new document
            $query = "UPDATE dokumen SET 
                     status = 'sampai',
                     current_handler = 'loket'
                     WHERE id = ?";
            break;
        case 'send':
            // When loket sends to verlap
            $query = "UPDATE dokumen SET 
                     status = 'pending',
                     current_handler = 'verlap'
                     WHERE id = ?";
            break;
    }
    $stmt = $conn->prepare($query);
    return $stmt->execute([$doc_id]);
}

function handleVerlapDocument($conn, $doc_id, $action) {
    switch($action) {
        case 'confirm':
            // When verlap confirms receipt
            $query = "UPDATE dokumen SET 
                     status = 'sampai'
                     WHERE id = ?";
            break;
        case 'send':
            // When verlap sends to penetapan
            $query = "UPDATE dokumen SET 
                     status = 'pending',
                     current_handler = 'penetapan'
                     WHERE id = ?";
            break;
    }
    $stmt = $conn->prepare($query);
    return $stmt->execute([$doc_id]);
}

function handlePenetapanDocument($conn, $doc_id, $action) {
    switch($action) {
        case 'confirm':
            $query = "UPDATE dokumen SET 
                     status = 'sampai'
                     WHERE id = ?";
            break;
        case 'send':
            // Send to kabid
            $query = "UPDATE dokumen SET 
                     status = 'pending',
                     current_handler = 'kabid'
                     WHERE id = ?";
            break;
    }
    $stmt = $conn->prepare($query);
    return $stmt->execute([$doc_id]);
}

function handleKabidDocument($conn, $doc_id, $action, $next_handler) {
    switch($action) {
        case 'confirm':
            $query = "UPDATE dokumen SET 
                     status = 'sampai'
                     WHERE id = ?";
            break;
        case 'send':
            // Send to specific handler (op_baru/mutasi1/mutasi2/bphtb)
            $query = "UPDATE dokumen SET 
                     status = 'pending',
                     current_handler = ?
                     WHERE id = ?";
            $stmt = $conn->prepare($query);
            return $stmt->execute([$next_handler, $doc_id]);
    }
    $stmt = $conn->prepare($query);
    return $stmt->execute([$doc_id]);
}

function handleFinalProcessor($conn, $doc_id) {
    // For op_baru, mutasi1, mutasi2, bphtb
    $query = "UPDATE dokumen SET 
             status = 'selesai',
             completion_date = CURRENT_TIMESTAMP
             WHERE id = ?";
    $stmt = $conn->prepare($query);
    return $stmt->execute([$doc_id]);
}

// Helper function to check document status
function getDocumentStatus($conn, $doc_id) {
    $query = "SELECT status, current_handler FROM dokumen WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$doc_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Usage example:
function processDocumentAction($conn, $doc_id, $user_role, $action, $next_handler = null) {
    switch($user_role) {
        case 'loket':
            return handleLoketDocument($conn, $doc_id, $action);
        case 'verlap':
            return handleVerlapDocument($conn, $doc_id, $action);
        case 'penetapan':
            return handlePenetapanDocument($conn, $doc_id, $action);
        case 'kabid':
            return handleKabidDocument($conn, $doc_id, $action, $next_handler);
        case 'op_baru':
        case 'mutasi1':
        case 'mutasi2':
        case 'bphtb':
            return handleFinalProcessor($conn, $doc_id);
        default:
            throw new Exception("Invalid role");
    }
}
?>